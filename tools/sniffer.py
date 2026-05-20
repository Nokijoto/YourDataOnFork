#!/usr/bin/env python3
"""
FORKED_DATA — Network Packet Capture Agent v1.0
================================================
Uruchamia się na routerze/laptopie Eryka i wysyła pakiety sieciowe
do dashboardu operatora (Rafał) przez webhook.

WYMAGANIA:
    pip install scapy requests

UŻYCIE:
    sudo python3 sniffer.py
    sudo python3 sniffer.py --interface wlan0 --webhook http://192.168.1.100/api/webhook/packet
    sudo python3 sniffer.py --interface eth0 --filter "port 80 or port 443 or port 53"

UWAGA: Wymaga uprawnień root (sudo) do przechwytywania pakietów.
"""

import argparse
import threading
import time
import sys
try:
    from scapy.all import sniff, IP, TCP, UDP, ICMP, DNS, DNSQR, ARP, Raw, Ether
    import requests
except ImportError:
    print("[!] Brak wymaganych bibliotek. Uruchom: pip install scapy requests")
    sys.exit(1)

# ─────────────────────────────────────────────
#  Konfiguracja
# ─────────────────────────────────────────────
DEFAULT_WEBHOOK   = "http://127.0.0.1/api/webhook/packet"
DEFAULT_INTERFACE = "wlan0"   # zmień: eth0, br-lan, wlan1, en0 (macOS)
DEFAULT_BPF       = ""        # BPF filter, np. "port 80 or port 53"
INTERESTING_ONLY  = True      # True = tylko HTTP/DNS/ARP, False = wszystko
BATCH_SIZE        = 1         # ile pakietów wysyłać naraz (1 = natychmiast)
TIMEOUT_S         = 2         # timeout requestu HTTP

# ─────────────────────────────────────────────
#  Kolejka wysyłki (nie blokuje sniffowania)
# ─────────────────────────────────────────────
import queue
send_queue = queue.Queue(maxsize=500)
stats = {"sent": 0, "dropped": 0, "errors": 0}


def sender_worker(webhook_url):
    """Wątek wysyłający pakiety z kolejki do webhooków."""
    session = requests.Session()
    session.headers.update({"Content-Type": "application/json", "User-Agent": "FORKED_DATA-Sniffer/1.0"})
    while True:
        data = send_queue.get()
        if data is None:
            break
        try:
            resp = session.post(webhook_url, json=data, timeout=TIMEOUT_S)
            if resp.status_code == 200:
                stats["sent"] += 1
            else:
                stats["errors"] += 1
        except Exception as e:
            stats["errors"] += 1
        finally:
            send_queue.task_done()


def push(data):
    """Wrzuca pakiet do kolejki (nie blokuje)."""
    try:
        send_queue.put_nowait(data)
    except queue.Full:
        stats["dropped"] += 1


# ─────────────────────────────────────────────
#  Parsowanie pakietów
# ─────────────────────────────────────────────
def process_packet(pkt):
    data = {
        "interface":       None,
        "protocol":        "OTHER",
        "src_ip":          None,
        "dst_ip":          None,
        "src_port":        None,
        "dst_port":        None,
        "packet_size":     len(pkt),
        "ttl":             None,
        "flags":           None,
        "payload_preview": None,
        "summary":         pkt.summary(),
        "raw":             {},
    }

    # Warstwa IP
    if IP in pkt:
        data["src_ip"] = pkt[IP].src
        data["dst_ip"] = pkt[IP].dst
        data["ttl"]    = pkt[IP].ttl

    # ARP (nie ma IP)
    elif ARP in pkt:
        data["protocol"] = "ARP"
        data["src_ip"]   = pkt[ARP].psrc
        data["dst_ip"]   = pkt[ARP].pdst
        data["summary"]  = f"ARP: Kto ma {pkt[ARP].pdst}? Mówi {pkt[ARP].psrc}"
        push(data)
        return

    # TCP
    if TCP in pkt:
        data["protocol"] = "TCP"
        data["src_port"] = pkt[TCP].sport
        data["dst_port"] = pkt[TCP].dport
        data["flags"]    = str(pkt[TCP].flags)

        dport = pkt[TCP].dport
        sport = pkt[TCP].sport

        if dport == 80 or sport == 80:
            data["protocol"] = "HTTP"
            # Spróbuj wyciągnąć metodę i URL
            if Raw in pkt:
                raw = bytes(pkt[Raw])
                try:
                    first_line = raw.split(b"\r\n")[0].decode("utf-8", errors="ignore")
                    data["summary"] = f"HTTP: {first_line}"
                except Exception:
                    pass
        elif dport == 443 or sport == 443:
            data["protocol"] = "HTTPS"
            data["summary"]  = f"HTTPS: {data['src_ip']}:{data['src_port']} → {data['dst_ip']}:{data['dst_port']}"
        elif dport == 22 or sport == 22:
            data["protocol"] = "SSH"
        elif dport == 3306 or sport == 3306:
            data["protocol"] = "MYSQL"
            data["summary"]  = f"MySQL: {data['src_ip']} → {data['dst_ip']}"

    # UDP
    elif UDP in pkt:
        data["protocol"] = "UDP"
        data["src_port"] = pkt[UDP].sport
        data["dst_port"] = pkt[UDP].dport

        # DNS
        if DNS in pkt and pkt[UDP].dport == 53:
            data["protocol"] = "DNS"
            try:
                qname = pkt[DNSQR].qname.decode("utf-8", errors="ignore").rstrip(".")
                data["summary"] = f"DNS query: {qname}"
                data["raw"]     = {"query": qname}
            except Exception:
                pass

    # ICMP
    elif ICMP in pkt:
        data["protocol"] = "ICMP"
        type_map = {0: "Echo Reply", 3: "Dest Unreachable", 8: "Echo Request", 11: "TTL Exceeded"}
        icmp_type = pkt[ICMP].type
        data["summary"] = f"ICMP {type_map.get(icmp_type, str(icmp_type))}: {data['src_ip']} → {data['dst_ip']}"

    # Payload preview (pierwsze 256 B jako hex)
    if Raw in pkt:
        raw_bytes = bytes(pkt[Raw])[:256]
        data["payload_preview"] = raw_bytes.hex()

    # Filtruj jeśli INTERESTING_ONLY
    interesting = ["HTTP", "HTTPS", "DNS", "ARP", "MYSQL", "SSH"]
    if INTERESTING_ONLY and data["protocol"] not in interesting:
        return

    push(data)


# ─────────────────────────────────────────────
#  Status ticker
# ─────────────────────────────────────────────
def status_ticker():
    while True:
        time.sleep(10)
        print(f"\r[*] Pakiety wysłane: {stats['sent']} | W kolejce: {send_queue.qsize()} | Błędy: {stats['errors']} | Porzucone: {stats['dropped']}", end="", flush=True)


# ─────────────────────────────────────────────
#  Main
# ─────────────────────────────────────────────
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="FORKED_DATA Packet Capture Agent")
    parser.add_argument("--interface", "-i", default=DEFAULT_INTERFACE, help="Interfejs sieciowy (domyślnie: wlan0)")
    parser.add_argument("--webhook",   "-w", default=DEFAULT_WEBHOOK,   help="URL webhooka operatora")
    parser.add_argument("--filter",    "-f", default=DEFAULT_BPF,       help="BPF filter (np. 'port 80 or port 53')")
    parser.add_argument("--all",       "-a", action="store_true",        help="Wysyłaj wszystkie pakiety (nie tylko HTTP/DNS/ARP)")
    args = parser.parse_args()

    if args.all:
        INTERESTING_ONLY = False

    print(f"""
╔══════════════════════════════════════════════════════╗
║         FORKED_DATA — Packet Capture Agent           ║
╠══════════════════════════════════════════════════════╣
║  Interface : {args.interface:<38} ║
║  Webhook   : {args.webhook:<38} ║
║  BPF Filter: {(args.filter or 'brak'):<38} ║
║  Tryb      : {'Wszystkie pakiety' if not INTERESTING_ONLY else 'Tylko HTTP/DNS/ARP/MySQL/SSH':<38} ║
╚══════════════════════════════════════════════════════╝
[*] Ctrl+C aby zatrzymać
""")

    # Wątek wysyłający
    sender = threading.Thread(target=sender_worker, args=(args.webhook,), daemon=True)
    sender.start()

    # Ticker statusu
    ticker = threading.Thread(target=status_ticker, daemon=True)
    ticker.start()

    try:
        sniff(
            iface=args.interface,
            filter=args.filter or None,
            prn=process_packet,
            store=False,
        )
    except KeyboardInterrupt:
        print(f"\n\n[!] Zatrzymano. Wysłano łącznie: {stats['sent']} pakietów.")
    except Exception as e:
        print(f"\n[!] Błąd: {e}")
        print("[!] Sprawdź czy masz uprawnienia root (sudo) i poprawną nazwę interfejsu.")
        sys.exit(1)
