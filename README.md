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

Windows CMD:

```cmd
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

Ardından `.env` dosyasını açarak gerekli bilgileri girin:

```env
DB_ROOT_PASSWORD=
DB_NAME=stajbilgisistem
DB_USERNAME=stajuser
DB_PASSWORD=
```

> `.env` dosyası güvenlik nedeniyle GitHub repository'sine gönderilmez. Gerçek veritabanı şifreleri bu dosyada saklanmalıdır.

### 3. Docker Container'larını Başlatma

Docker Desktop'ın çalıştığından emin olun.

Proje klasöründe aşağıdaki komutu çalıştırın:

```bash
docker compose up -d --build
```

Bu komut web uygulaması ve MariaDB veritabanı için gerekli container'ları oluşturur ve başlatır.

Container'ların durumunu kontrol etmek için:

```bash
docker compose ps
```

### 4. Uygulamaya Erişim

Container'lar başarıyla başlatıldıktan sonra uygulamaya aşağıdaki adres üzerinden erişebilirsiniz:

```text
http://localhost:8080/?url=login
```

Bu adres üzerinden giriş ekranına ulaşabilirsiniz.

## Container'lar

Proje iki temel Docker container'ı kullanmaktadır:

| Container | Görevi |
|---|---|
| `stajyonetim-web` | PHP ve Apache web uygulaması |
| `stajyonetim-db` | MariaDB veritabanı |

## Proje Yapısı

```text
StajYonetimSistemi/
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
- Öğrenci yönetimi
- Staj başvuru işlemleri
- Staj dönemi yönetimi
- Danışman / supervisor yönetimi
- Staj değerlendirme işlemleri
- Günlük staj kayıtları
- İzin ve devamsızlık işlemleri
- Proje teslim işlemleri
- IP doğrulama
- Yönetici paneli
- MVC tabanlı yapı
- MariaDB veritabanı
- Docker ile konteynerleştirilmiş çalışma ortamı

## Veritabanı

Projenin veritabanı MariaDB üzerinde çalışmaktadır.

Veritabanı başlangıçta `database/` klasöründeki SQL dosyaları kullanılarak Docker tarafından oluşturulur.

Veritabanı bağlantı bilgileri `.env` dosyasından alınır.

> Hassas veritabanı bilgileri GitHub repository'sinde tutulmaz.

## Container'ları Durdurma

Container'ları durdurmak için:

```bash
docker compose down
```

## Container'ları Yeniden Başlatma

Container'ları tekrar başlatmak için:

```bash
docker compose up -d
```

## Container'ları Yeniden Oluşturma

Docker yapılandırmasında değişiklik yapıldığında:

```bash
docker compose up -d --build
```

## Lisans

Bu proje eğitim ve staj kapsamında geliştirilmiştir.