# Stajyer Yönetim Sistemi

PHP ve MariaDB kullanılarak geliştirilen, Docker ile konteynerleştirilmiş Stajyer Yönetim Sistemi.

## Kullanılan Teknolojiler

- PHP
- MariaDB
- Docker
- Docker Compose
- HTML
- CSS
- JavaScript
- MVC Mimarisi

## Gereksinimler

Projeyi çalıştırabilmek için aşağıdaki yazılımların kurulu olması gerekir:

- Git
- Docker Desktop

> PHP, Apache ve MariaDB'nin ayrıca kurulmasına gerek yoktur. Gerekli servisler Docker üzerinden çalıştırılır.

## Kurulum

### 1. Projeyi Klonlama

Terminali açın ve projeyi bilgisayarınıza indirin:

```bash
git clone https://github.com/osmanaslann01/stajyer-yonetim-sistemi.git
```

Proje klasörüne girin:

```bash
cd stajyer-yonetim-sistemi
```

### 2. Ortam Değişkenlerinin Ayarlanması

Proje, veritabanı bağlantı bilgilerini `.env` dosyası üzerinden kullanır.

Repository içerisinde bulunan `.env.example` dosyasını `.env` olarak kopyalayın.

#### Windows CMD

```cmd
copy .env.example .env
```

#### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

Ardından `.env` dosyasını açın:

```env
DB_ROOT_PASSWORD=admin
DB_NAME=stajbilgisistem
DB_USERNAME=stajuser
DB_PASSWORD=staj
```

> `.env` dosyası güvenlik nedeniyle GitHub repository'sine gönderilmez. Gerçek veritabanı şifreleri bu dosyada saklanmalıdır.

### 3. Docker Container'larını Başlatma

Docker Desktop'ın çalıştığından emin olun.

Proje klasöründe aşağıdaki komutu çalıştırın:

```bash
docker compose up -d --build
```

Bu komut:

- PHP ve Apache web container'ını oluşturur.
- MariaDB container'ını oluşturur.
- Gerekli Docker ağı ve volume'larını oluşturur.
- Veritabanını `database/` klasöründeki SQL dosyası ile başlatır.
- Web uygulamasını çalıştırır.

### 4. Container Durumunu Kontrol Etme

Container'ların çalışıp çalışmadığını kontrol etmek için:

```bash
docker compose ps
```

Aşağıdaki container'ların çalışıyor olması beklenir:

```text
stajyonetim-web
stajyonetim-db
```

### 5. Uygulamaya Erişim

Container'lar başarıyla çalıştıktan sonra tarayıcıdan aşağıdaki adresi açın:

```text
http://localhost:8080/?url=login
```

Giriş ekranına bu adres üzerinden ulaşabilirsiniz.

## Varsayılan Test Kullanıcısı

Projede test amacıyla kullanılan kullanıcı bilgileri veritabanındaki SQL dosyasında bulunmaktadır.

> Güvenlik nedeniyle gerçek kullanıcı şifrelerinin README dosyasında paylaşılması önerilmez.

## Docker Container'ları

Proje iki temel Docker container'ı kullanmaktadır:

| Container | Görevi |
|---|---|
| `stajyonetim-web` | PHP ve Apache web uygulaması |
| `stajyonetim-db` | MariaDB veritabanı |

Web uygulaması:

```text
localhost:8080
```

MariaDB container içerisindeki standart MariaDB portunu kullanır:

```text
3306
```

> MariaDB portu host bilgisayara doğrudan açılmamıştır. Web container'ı veritabanına Docker ağı üzerinden `db` hostname'i ile bağlanır.

## Veritabanı Yapılandırması

Uygulamanın veritabanı bağlantısı ortam değişkenleri üzerinden yapılmaktadır.

`config/database.php` dosyası aşağıdaki değişkenleri kullanır:

```text
DB_HOST
DB_NAME
DB_USERNAME
DB_PASSWORD
```

Docker ortamında:

```text
DB_HOST=db
```

olarak ayarlanır.

Docker Compose içerisindeki veritabanı servisi:

```yaml
db:
  image: mariadb:12.3
```

şeklinde MariaDB 12.3 imajını kullanmaktadır.

## Veritabanının İlk Oluşturulması

Docker Compose ilk kez çalıştırıldığında `database/` klasörü içerisindeki SQL dosyaları MariaDB container'ına aktarılır.

Docker Compose yapılandırmasında:

```yaml
volumes:
  - ./database:/docker-entrypoint-initdb.d
```

kullanıldığı için MariaDB ilk başlatıldığında SQL dosyaları otomatik olarak çalıştırılır.

> MariaDB volume'u daha önce oluşturulmuşsa SQL dosyaları tekrar otomatik olarak çalıştırılmaz.

## Proje Yapısı

```text
stajyer-yonetim-sistemi/
│
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
│
├── config/
│   └── database.php
│
├── core/
│   ├── Controller.php
│   ├── Model.php
│   ├── Router.php
│   └── ...
│
├── database/
│   └── *.sql
│
├── public/
│   └── index.php
│
├── .env.example
├── .gitignore
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Özellikler

- Kullanıcı giriş sistemi
- Kullanıcı kayıt sistemi
- Öğrenci yönetimi
- Staj başvuru işlemleri
- Staj dönemi yönetimi
- Danışman / sorumlu yönetimi
- Staj değerlendirme işlemleri
- Günlük staj kayıtları
- İzin işlemleri
- Devamsızlık işlemleri
- Proje atama işlemleri
- Proje teslim işlemleri
- IP doğrulama
- Yönetici paneli
- Şifre sıfırlama işlemleri
- Sistem loglama
- Bildirim sistemi
- SMS loglama altyapısı
- MVC tabanlı yapı
- MariaDB veritabanı
- Docker ile konteynerleştirilmiş çalışma ortamı

## Kullanıcı Rolleri

Sistemde temel olarak üç farklı kullanıcı rolü bulunmaktadır:

| Rol | Açıklama |
|---|---|
| Admin | Sistem yöneticisi |
| Öğrenci | Staj yapan öğrenci |
| Sorumlu | Staj sürecini yöneten sorumlu / danışman |

## Uygulamayı Durdurma

Çalışan container'ları durdurmak için:

```bash
docker compose down
```

Bu komut container'ları durdurur ve kaldırır ancak veritabanı volume'unu silmez.

## Uygulamayı Yeniden Başlatma

Container'lar daha önce oluşturulduysa:

```bash
docker compose up -d
```

komutu ile tekrar başlatılabilir.

## Docker Yapılandırmasını Yeniden Oluşturma

`Dockerfile` veya `docker-compose.yml` üzerinde değişiklik yapıldığında:

```bash
docker compose up -d --build
```

komutu kullanılabilir.

## Container Loglarını Görüntüleme

Web container'ının loglarını görüntülemek için:

```bash
docker compose logs web
```

MariaDB container'ının loglarını görüntülemek için:

```bash
docker compose logs db
```

Tüm servislerin loglarını görüntülemek için:

```bash
docker compose logs
```

Logları canlı olarak takip etmek için:

```bash
docker compose logs -f
```

## Container Durumunu Kontrol Etme

Çalışan container'ları görüntülemek için:

```bash
docker compose ps
```

Docker tarafından oluşturulan container'ları görmek için:

```bash
docker ps
```

## Veritabanını Sıfırlama

Veritabanını ve Docker volume'unu tamamen silmek için:

```bash
docker compose down -v
```

Ardından sistemi yeniden oluşturmak için:

```bash
docker compose up -d --build
```

> DİKKAT: `docker compose down -v` komutu MariaDB volume'unu siler. Bu nedenle veritabanındaki mevcut veriler kaybolur. Geliştirme ve test ortamlarında dikkatli kullanılmalıdır.

## Yeni Bir Bilgisayarda Çalıştırma

Projeyi başka bir bilgisayarda çalıştırmak için:

### 1. GitHub repository'sini klonlayın

```bash
git clone https://github.com/osmanaslann01/stajyer-yonetim-sistemi.git
```

### 2. Proje klasörüne girin

```bash
cd stajyer-yonetim-sistemi
```

### 3. `.env` dosyasını oluşturun

Windows CMD:

```cmd
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

### 4. Docker container'larını oluşturun

```bash
docker compose up -d --build
```

### 5. Uygulamayı açın

```text
http://localhost:8080/?url=login
```

Bu işlemlerden sonra PHP, Apache ve MariaDB'yi ayrıca kurmaya gerek kalmadan proje Docker üzerinden çalıştırılabilir.

## Sorun Giderme

### Container'lar çalışmıyorsa

Öncelikle Docker Desktop'ın açık olduğundan emin olun.

Ardından:

```bash
docker compose ps
```

komutu ile container durumlarını kontrol edin.

### Web uygulamasında hata varsa

Web container'ının loglarını kontrol edin:

```bash
docker compose logs web
```

### Veritabanı bağlantı hatası varsa

MariaDB container'ının loglarını kontrol edin:

```bash
docker compose logs db
```

Ayrıca `.env` dosyasındaki bilgilerin `docker-compose.yml` içerisindeki yapılandırmayla uyumlu olduğundan emin olun.

### Veritabanını yeniden oluşturmak gerekiyorsa

```bash
docker compose down -v
docker compose up -d --build
```

> Bu işlem mevcut veritabanı verilerini siler.

## Güvenlik

`.env` dosyası hassas bilgileri içerdiği için Git repository'sine gönderilmemelidir.

Repository içerisinde yalnızca:

```text
.env.example
```

dosyası bulunmalıdır.

`.env` dosyası `.gitignore` içerisinde yer almalıdır.

Örnek:

```text
.env
```

Gerçek veritabanı şifreleri ve diğer gizli bilgiler GitHub üzerinde paylaşılmamalıdır.

## Lisans

Bu proje eğitim ve staj kapsamında geliştirilmiştir.