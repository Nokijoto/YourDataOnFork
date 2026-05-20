<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // Seed default Sherlock services
        $services = [
            ['name' => 'GitHub', 'url_pattern' => 'https://github.com/{}'],
            ['name' => 'Twitter / X', 'url_pattern' => 'https://x.com/{}'],
            ['name' => 'Reddit', 'url_pattern' => 'https://reddit.com/user/{}'],
            ['name' => 'Instagram', 'url_pattern' => 'https://instagram.com/{}'],
            ['name' => 'TikTok', 'url_pattern' => 'https://tiktok.com/@{}'],
            ['name' => 'LinkedIn', 'url_pattern' => 'https://linkedin.com/in/{}'],
            ['name' => 'YouTube', 'url_pattern' => 'https://youtube.com/@{}'],
            ['name' => 'Twitch', 'url_pattern' => 'https://twitch.tv/{}'],
            ['name' => 'Steam', 'url_pattern' => 'https://steamcommunity.com/id/{}'],
            ['name' => 'GitLab', 'url_pattern' => 'https://gitlab.com/{}'],
            ['name' => 'Keybase', 'url_pattern' => 'https://keybase.io/{}'],
            ['name' => 'Medium', 'url_pattern' => 'https://medium.com/@{}'],
            ['name' => 'Pinterest', 'url_pattern' => 'https://pinterest.com/{}'],
            ['name' => 'Telegram', 'url_pattern' => 'https://t.me/{}'],
            ['name' => 'Wykop', 'url_pattern' => 'https://wykop.pl/ludzie/{}'],
            ['name' => 'Allegro Lokalnie', 'url_pattern' => 'https://allegrolokalnie.pl/uzytkownik/{}'],
        ];

        $servicesByName = collect($services)->keyBy('name');

        foreach ($this->sherlockServiceNames() as $serviceName) {
            $servicesByName->put($serviceName, [
                'name' => $serviceName,
                'url_pattern' => $servicesByName[$serviceName]['url_pattern'] ?? $this->fallbackSherlockUrlPattern($serviceName),
            ]);
        }

        $services = $servicesByName->values()->all();

        $serviceModels = [];
        foreach ($services as $service) {
            $serviceModels[$service['name']] = \App\Models\SherlockService::updateOrCreate(
                ['name' => $service['name']],
                ['url_pattern' => $service['url_pattern'], 'is_active' => true]
            );
        }

        // Seed default Sherlock rules
        $rules = [
            [
                'username' => 'admin',
                'service' => 'GitHub',
                'is_found' => true,
            ],
            [
                'username' => 'admin',
                'service' => 'Twitter / X',
                'is_found' => true,
            ],
            [
                'username' => 'admin',
                'service' => 'Reddit',
                'is_found' => false,
            ],
            [
                'username' => 'guest',
                'service' => 'Reddit',
                'is_found' => true,
            ],
            [
                'username' => 'guest',
                'service' => 'Instagram',
                'is_found' => true,
            ],
            [
                'username' => 'guest',
                'service' => 'GitHub',
                'is_found' => false,
            ],
            ['username' => 'j.kowalski', 'service' => 'GitHub', 'is_found' => true],
            ['username' => 'j.kowalski', 'service' => 'LinkedIn', 'is_found' => true],
            ['username' => 'j.kowalski', 'service' => 'Twitter / X', 'is_found' => true],
            ['username' => 'j.kowalski', 'service' => 'Instagram', 'is_found' => false],
            ['username' => 'j.kowalski', 'service' => 'Wykop', 'is_found' => true],
            ['username' => 'janek1998', 'service' => 'Steam', 'is_found' => true],
            ['username' => 'janek1998', 'service' => 'Twitch', 'is_found' => true],
            ['username' => 'janek1998', 'service' => 'YouTube', 'is_found' => false],
            ['username' => 'janek1998', 'service' => 'Reddit', 'is_found' => true],
            ['username' => 'studentka_ola', 'service' => 'Instagram', 'is_found' => true],
            ['username' => 'studentka_ola', 'service' => 'TikTok', 'is_found' => true],
            ['username' => 'studentka_ola', 'service' => 'Pinterest', 'is_found' => true],
            ['username' => 'studentka_ola', 'service' => 'LinkedIn', 'is_found' => false],
            ['username' => 'rooted_mati', 'service' => 'GitHub', 'is_found' => true],
            ['username' => 'rooted_mati', 'service' => 'GitLab', 'is_found' => true],
            ['username' => 'rooted_mati', 'service' => 'Keybase', 'is_found' => true],
            ['username' => 'rooted_mati', 'service' => 'Telegram', 'is_found' => true],
            ['username' => 'ania.dev', 'service' => 'GitHub', 'is_found' => true],
            ['username' => 'ania.dev', 'service' => 'Medium', 'is_found' => true],
            ['username' => 'ania.dev', 'service' => 'LinkedIn', 'is_found' => true],
            ['username' => 'ania.dev', 'service' => 'Steam', 'is_found' => false],
        ];

        foreach ($rules as $rule) {
            $service = $serviceModels[$rule['service']] ?? null;
            if ($service) {
                \App\Models\SherlockRule::updateOrCreate(
                    [
                        'username' => $rule['username'],
                        'service_id' => $service->id,
                    ],
                    [
                        'is_found' => $rule['is_found'],
                    ]
                );
            }
        }

        // Seed default HIBP breaches
        $breaches = [
            [
                'name' => 'Adobe',
                'breach_date' => 'October 2013',
                'compromised_data' => 'Email addresses, Passwords, Password hints, Usernames',
            ],
            [
                'name' => 'LinkedIn',
                'breach_date' => 'May 2016',
                'compromised_data' => 'Email addresses, Passwords',
            ],
            [
                'name' => 'Canva',
                'breach_date' => 'May 2019',
                'compromised_data' => 'Email addresses, Names, Passwords, Usernames',
            ],
            [
                'name' => 'Zynga',
                'breach_date' => 'September 2019',
                'compromised_data' => 'Email addresses, Passwords, Phone numbers, Usernames',
            ],
            [
                'name' => 'Dropbox',
                'breach_date' => 'July 2012',
                'compromised_data' => 'Email addresses, Passwords',
            ],
            [
                'name' => 'Morele.net',
                'breach_date' => 'November 2018',
                'compromised_data' => 'Names, Email addresses, Phone numbers, Physical addresses, Encrypted passwords',
            ],
            [
                'name' => 'Allegro Archive Demo',
                'breach_date' => 'Demo dataset',
                'compromised_data' => 'Email addresses, Usernames, Phone numbers',
            ],
            [
                'name' => 'CD Projekt Forum',
                'breach_date' => 'March 2016',
                'compromised_data' => 'Email addresses, Usernames, Passwords',
            ],
            [
                'name' => 'MyFitnessPal',
                'breach_date' => 'February 2018',
                'compromised_data' => 'Email addresses, Usernames, Password hashes',
            ],
            [
                'name' => 'Dubsmash',
                'breach_date' => 'December 2018',
                'compromised_data' => 'Email addresses, Usernames, Password hashes, Dates of birth',
            ],
            [
                'name' => 'Collection #1',
                'breach_date' => 'January 2019',
                'compromised_data' => 'Email addresses, Passwords',
            ],
            [
                'name' => 'Gravatar Scrape',
                'breach_date' => 'October 2020',
                'compromised_data' => 'Email addresses, Names, Usernames',
            ],
            [
                'name' => 'Apollo',
                'breach_date' => 'July 2018',
                'compromised_data' => 'Email addresses, Employers, Job titles, Names, Phone numbers',
            ],
        ];

        $breachModels = [];
        foreach ($breaches as $breach) {
            $breachModels[$breach['name']] = \App\Models\PwnedBreach::updateOrCreate(
                ['name' => $breach['name']],
                [
                    'breach_date' => $breach['breach_date'],
                    'compromised_data' => $breach['compromised_data'],
                    'is_active' => true,
                ]
            );
        }

        // Seed default HIBP rules
        $pwnedRules = [
            [
                'email' => 'admin@example.com',
                'breach' => 'Adobe',
                'is_pwned' => true,
            ],
            [
                'email' => 'admin@example.com',
                'breach' => 'Canva',
                'is_pwned' => true,
            ],
            [
                'email' => 'test@example.com',
                'breach' => 'LinkedIn',
                'is_pwned' => true,
            ],
            [
                'email' => 'test@example.com',
                'breach' => 'Dropbox',
                'is_pwned' => true,
            ],
            ['email' => 'jan.kowalski@gmail.com', 'breach' => 'Morele.net', 'is_pwned' => true],
            ['email' => 'jan.kowalski@gmail.com', 'breach' => 'LinkedIn', 'is_pwned' => true],
            ['email' => 'jan.kowalski@gmail.com', 'breach' => 'Collection #1', 'is_pwned' => true],
            ['email' => 'jan.kowalski@gmail.com', 'breach' => 'Canva', 'is_pwned' => false],
            ['email' => 'ola.nowak@student.edu.pl', 'breach' => 'Morele.net', 'is_pwned' => true],
            ['email' => 'ola.nowak@student.edu.pl', 'breach' => 'Dubsmash', 'is_pwned' => true],
            ['email' => 'ola.nowak@student.edu.pl', 'breach' => 'Gravatar Scrape', 'is_pwned' => true],
            ['email' => 'mati.root@example.com', 'breach' => 'Adobe', 'is_pwned' => true],
            ['email' => 'mati.root@example.com', 'breach' => 'CD Projekt Forum', 'is_pwned' => true],
            ['email' => 'mati.root@example.com', 'breach' => 'MyFitnessPal', 'is_pwned' => false],
            ['email' => 'ania.dev@example.com', 'breach' => 'Apollo', 'is_pwned' => true],
            ['email' => 'ania.dev@example.com', 'breach' => 'Gravatar Scrape', 'is_pwned' => true],
            ['email' => 'ania.dev@example.com', 'breach' => 'Dropbox', 'is_pwned' => false],
            ['email' => 'konferencja.demo@forked.test', 'breach' => 'Morele.net', 'is_pwned' => true],
            ['email' => 'konferencja.demo@forked.test', 'breach' => 'Allegro Archive Demo', 'is_pwned' => true],
            ['email' => 'konferencja.demo@forked.test', 'breach' => 'Collection #1', 'is_pwned' => true],
        ];

        foreach ($pwnedRules as $rule) {
            $breach = $breachModels[$rule['breach']] ?? null;
            if ($breach) {
                \App\Models\PwnedRule::updateOrCreate(
                    [
                        'email' => $rule['email'],
                        'breach_id' => $breach->id,
                    ],
                    [
                        'is_pwned' => $rule['is_pwned'],
                    ]
                );
            }
        }
    }

    /**
     * Large demo catalogue based on Sherlock-style profile providers.
     *
     * @return array<int, string>
     */
    private function sherlockServiceNames(): array
    {
        $names = <<<'SERVICES'
1337x
2Dimensions
7Cups
9GAG
APClips (NSFW)
AWS Skills Profile
About.me
Academia.edu
AdmireMe.Vip (NSFW)
Airbit
Airliners
All Things Worn (NSFW)
AllMyLinks
AniWorld
Anilist
Aparat
Apple Developer
Apple Discussions
Archive of Our Own
Archive.org
Arduino Forum
ArtStation
Asciinema
Ask Fedora
Atcoder
Audiojungle
Autofrage
Avizo
BOOTH
BabyRu
Bandcamp
Bazar.cz
Behance
Bezuzyteczna
BiggerPockets
BioHacking
BitBucket
Bitwarden Forum
Blipfoto
Blitz Tactics
Blogger
Bluesky
BoardGameGeek
BongaCams (NSFW)
Bookcrossing
BraveCommunity
BreachSta.rs Forum
BugCrowd
BuyMeACoffee
BuzzFeed
CGTrader
CNET
CSSBattle
CTAN
Caddy Community
Car Talk Community
Carbonmade
Career.habr
Carrd
CashApp
Cfx.re Forum
Championat
Chaos
Chatujme.cz
ChaturBate (NSFW)
Chess
Choice Community
Chollometro
Clapper
CloudflareCommunity
Clozemaster
Clubhouse
Code Snippet Wiki
CodeSandbox
Codeberg
Codecademy
Codechef
Codeforces
Codepen
Coders Rank
Coderwall
Codewars
Codolio
Coinvote
ColourLovers
Contently
Coroflot
Cplusplus
Cracked
Cracked Forum
Credly
Crevado
Crowdin
CryptoHack
Cryptomator Forum
Cults3D
CurseForge
CyberDefenders
DEV Community
DMOJ
DailyMotion
Dealabs
DeviantArt
DigitalSpy
Discogs
Discord
Discord.bio
Discuss.Elastic.co
Diskusjon.no
Disqus
Docker Hub
Dribbble
Duolingo
Eintracht Frankfurt Forum
Empretienda AR
Envato Forum
Erome (NSFW)
Exposure
EyeEm
F3.cool
Fameswap
Fandom
Fanpop
Finanzfrage
Flickr
Flightradar24
Flipboard
Football
FortniteTracker
Forum Ophilia (NSFW)
Fosstodon
Framapiaf
Freelancer
Freesound
GNOME VCS
GaiaOnline
GameFAQs
Gamespot
GeeksforGeeks
Genius (Artists)
Genius (Users)
Gesundheitsfrage
GetMyUni
Giant Bomb
Giphy
GitBook
GitHub
GitLab
Gitea
Gitee
GoodReads
Google Play
Gradle
Grailed
Gravatar
Gumroad
Gutefrage
HackMD
HackTheBox
Hackaday
HackenProof (Hackers)
HackerEarth
HackerNews
HackerOne
HackerRank
HackerSploit
Harvard Scholar
Hashnode
Heavy-R (NSFW)
Hive Blog
Holopin
HotUKdeals
Houzz
HubPages
Hubski
HudsonRock
Hugging Face
IFTTT
IRC-Galleria
Icons8 Community
Ifunny
Image Fap (NSFW)
ImgUp.cz
Imgur
Instagram
Instapaper
Instructables
Intigriti
Ionic Forum
Issuu
Itch.io
Itemfix
Jellyfin Weblate
Jimdo
Joplin Forum
Jupyter Community Forum
Kaggle
Keybase
Kick
Kik
Kongregate
Kvinneguiden
LOR
Laracast
Launchpad
LeetCode
LemmyWorld
LessWrong
Letterboxd
LibraryThing
Lichess
LinkedIn
Linktree
LinuxFR.org
Listed
LiveJournal
Lobsters
LottieFiles
LushStories (NSFW)
MMORPG Forum
Mamot
Medium
Memrise
Minecraft
MixCloud
Monkeytype
Motherless (NSFW)
Motorradfrage
MuseScore
MyAnimeList
MyMiniFactory
Mydealz
Mydramalist
Myspace
NICommunityForum
NationStates Nation
NationStates Region
Naver
Needrom
Newgrounds
Nextcloud Forum
Nightbot
Ninja Kiwi
NintendoLife
NitroType
NotABug.org
Nothing Community
Nyaa.si
ObservableHQ
Odysee
Open Collective
OpenGameArt
OpenStreetMap
Opensource
OurDJTalk
Outgress
PCGamer
PSNProfiles.com
Packagist
Pastebin
Patched
Patreon
PentesterLab
PepperNL
PepperPL
Pepperdeals
PepperealsUS
Periscope
Pinkbike
Pinterest
Platzi
PlayStore
Playstrategy
Plurk
PocketStars (NSFW)
Pokemon Showdown
Polarsteps
Polygon
Polymart
Pornhub (NSFW)
Preisjaeger
ProductHunt
PromoDJ
Promodescuentos
Pronouns.page
PyPi
Pychess
Python.org Discussions
Rajce.net
Rarible
Rate Your Music
Rclone Forum
Realmeye
RedTube (NSFW)
Redbubble
Reddit
Reisefrage
Replit.com
ResearchGate
ReverbNation
Roblox
RocketTube (NSFW)
RoyalCams
Ruby Forums
RubyGems
Rumble
RuneScape
SEOForum
SOOP
SWAPD
Sbazar.cz
Scratch
Scribd
Shelf
ShitpostBot5000
Signal
Sketchfab
Slack
Slant
Slashdot
SlideShare
Slides
SmugMug
Smule
Snapchat
SoundCloud
SourceForge
SoylentNews
SpaceHey
SpeakerDeck
Speedrun.com
Spells8
Splice
Splits.io
Sporcle
Sportlerfrage
SportsRU
Spotify
Star Citizen
Status Cafe
Steam Community (Group)
Steam Community (User)
Strava
SublimeForum
Substack
TETR.IO
TRAKTRAIN
Telegram
Tellonym.me
Tenor
Terraria Forums
TheMovieDB
ThemeForest
Tiendanube
TikTok
TnAFlix (NSFW)
Topcoder
Topmate
TradingView
Trakt
TrashboxRU
Trawelling
Trello
Trovo
TryHackMe
Tuna
Tweakers
Twitch
Twitter
Typeracer
Ultimate-Guitar
Unsplash
Untappd
VK
VLR
VSCO
Valorant Forums
Velog
Velomania
Venmo
Vero
Vimeo
VirusTotal
Vjudge
WICG Forum
Wakatime
Warframe Market
Warrior Forum
Wattpad
WebNode
Weblate
Weebly
Wikidot
Wikipedia
Windy
Wix
WolframalphaForum
WordPress
WordPressOrg
Wordnik
Wowhead
Wykop
Xbox Gamertag
Xvideos (NSFW)
YandexMusic
YouNow
YouPic
YouPorn (NSFW)
YouTube
addons.wago.io
akniga
authorSTREAM
babyblogRU
chaos.social
couchsurfing
d3RU
dailykos
datingRU
dcinside
devRant
drive2
eGPU
eintracht
exophase
fixya
fl
forum_guns
freecodecamp
furaffinity
geocaching
habr
hackster
hunting
igromania
imood
interpals
irecommend
jbzd.com.pl
jeuxvideo
kaskus
kofi
kwork
last.fm
leasehackr
livelib
mastodon.cloud
mastodon.social
mastodon.xyz
mercadolivre
minds
moikrug
mstdn.io
mstdn.social
n8n Community
nairaland.com
namuwiki
nnRU
note
npm
omg.lol
opennet
osu!
phpRU
pikabu
pixelfed.social
pr0gramm
prog.hu
programming.dev
satsisRU
sessionize
social.tchncs.de
spletnik
svidbook
threads
tistory
toster
tumblr
uid
write.as
xHamster (NSFW)
znanylekarz.pl
SERVICES;

        return collect(preg_split('/\R/', $names))
            ->map(fn (string $name): string => trim($name))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function fallbackSherlockUrlPattern(string $serviceName): string
    {
        $cleanName = preg_replace('/\s*\(NSFW\)\s*/i', '', $serviceName) ?: $serviceName;
        $slug = strtolower($cleanName);
        $slug = str_replace(['&', '+'], ['and', 'plus'], $slug);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?: 'profile';
        $slug = trim($slug, '-');

        return "https://www.google.com/search?q={$slug}+{}";
    }
}
