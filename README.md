# Tradely Front-backend

![Grafika_glowna](https://raw.githubusercontent.com/Milosz1532/tradely-frontend/master/APP_SCREENS/Screenshot_10.png)

Tradely is a dynamic web application designed to allow users to add and browse advertisements. It is a classifieds portal aimed at showcasing skills in creating advanced web applications. This project was developed solely by me as a portfolio piece to demonstrate my backend development skills.

With Tradely, users can easily add new advertisements, browse existing listings, and use search functionality to find products or services of interest. The backend, developed using Laravel, powers these functionalities and ensures smooth communication with the front-end.

## Application Features:
- Account login and registration system: Secure user authentication and registration process using JWT.
- Add free advertisements: Users can post ads for sale, exchange, and free offers.
- Advanced search functionality: Dynamic filters tailored to each category and subcategory.
- Location-based search: Find advertisements based on geographical location.
- Live chat: Real-time communication between users powered by WebSockets and Laravel.
- Ad management: Like and manage advertisements efficiently.
- User permissions and roles: Administrative functions for editing, deleting ads, blocking users, and reporting content.

## Installation and Setup

A step by step series of examples that tell you how to get a development
environment running


- Clone the repository:

```
git clone https://github.com/Milosz1532/tradely-backend.git
```

- Navigate to the project directory:

```
cd tradely-backend
```

- Install dependencies:

```
composer install
```

- Copy the example environment file and set up your environment variables:

```
cp .env.example .env
```

- Generate the application key:
```
php artisan key:generate
```

- Run the database migrations:
```
php artisan migrate
```

- Start the development server:
```
php artisan serve
```

## Technologies Used:
- Laravel
- MySQL
- JTW (JSON Web Tokens)
- WebSockets

[![Technologies](https://skillicons.dev/icons?i=laravel,mysql)](https://skillicons.dev)

## Frontend

The frontend was developed by me using React.js, and the project is available as a separate repository at: https://github.com/Milosz1532/tradely-frontend.

