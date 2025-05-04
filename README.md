# Babacare

Babacare is a platform that enables healthcare providers to effectively monitor and manage baby health records while facilitating communication with parents. It features user authentication, baby profiles, growth monitoring, vaccination tracking, and secure communication channels.

---

## Features

- User-friendly dashboard  
- Service management  
- Secure authentication  
- Responsive design  

---

## Home Page Preview

![Home Page](public/images/users/home%20page.png)

---

## Installation & Setup

1. **Update Composer dependencies:**
   ```sh
   composer update
   ```

2. **Copy the example environment file and configure your environment variables:**
   ```sh
   cp .env.example .env
   ```

3. **Generate the application key:**
   ```sh
   php artisan key:generate
   ```

4. **Create a database:**
   - Create a database named `babycare` using your preferred database tool.

5. **Update your `.env` file:**
   ```
   DB_DATABASE='babycare'
   ```

6. **Import the database:**
   - In the project directory, locate the `sql` folder.
   - Use phpMyAdmin (or another tool) to import the SQL file into the `babycare` database.

7. **Start the development server:**
   ```sh
   php artisan serve
   ```

8. **Open your browser and navigate to:**
   ```
   http://localhost:8000
   ```

---

## Admin Credentials

- **Username:** `admin@mail.com`
- **Password:** `123456`

---

---

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or bug fixes.

---

