# UnionVote - Online Voting System

An online voting system built for the IT Faculty Union at Nha Trang University.

## Requirements

- PHP >= 8.1
- MySQL 5.7+ / MariaDB 10.6+
- Composer
- Apache (mod_rewrite)

## Installation

```bash
git clone https://github.com/YOUR_USERNAME/UnionVote.git
cd UnionVote
composer install
cp .env.example .env
```

Edit `.env` with your database credentials and domain.

Create database and import data:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p unionvote < database/seed.sql
```

Point your web server DocumentRoot to the `public/` directory.

## Admin Login

- URL: `/admin/login`
- Username: `admin`
- Password: `Admin@123`

Change the password immediately after first login.

## Tech Stack

- PHP (MVC Pattern)
- MySQL
- Tailwind CSS
- PHPMailer
- PhpSpreadsheet

## License

MIT License
