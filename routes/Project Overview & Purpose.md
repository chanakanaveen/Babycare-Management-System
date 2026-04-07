Here are the answers to all the viva questions based on your project:

---

## Answers to Viva Questions

---

### Project Overview & Purpose

**1. What problem does your system solve?**
> Traditional baby health monitoring in Sri Lanka relies on paper-based records which are error-prone, hard to access, and inefficient. My system digitizes the entire process — parents can track their baby's growth, midwives can manage vaccinations and appointments, and MOH officers can oversee their division. It eliminates manual record-keeping and improves communication between healthcare providers and parents.

**2. Why did you choose a web-based system over a mobile app?**
> A web-based system is accessible on any device (phone, tablet, PC) without installation. It's easier to maintain — one codebase serves all users. Midwives and MOH officers work on desktops, while parents can access it from any browser on their phone. No app store approval is needed for updates.

**3. What are the 5 user roles in your system?**
> - **Admin** — Full system control: manages all users, notices, system settings
> - **MOH (Medical Officer of Health)** — Oversees midwives in their division, monitors coverage
> - **Midwife** — Manages assigned babies, records vaccinations, handles appointments, chats with parents
> - **Parent** — Registers babies, views growth records, books appointments, chats with midwife
> - **Seller** — (Legacy role from earlier project version — could be a healthcare product seller)

**4. What were the functional and non-functional requirements?**
> **Functional:** User registration/login, baby profile management, growth record tracking, vaccination scheduling, appointment booking, real-time chat, AI-based growth prediction, email/SMS notifications.
> **Non-functional:** Security (authenticated routes, CSRF), scalability (queued jobs), reliability (API fallback for AI), usability (responsive design).

---

### Technology Stack

**5. Why did you choose Laravel?**
> Laravel follows MVC architecture, provides built-in features like authentication, routing, ORM (Eloquent), mailing, queues, and broadcasting out of the box. It has excellent documentation, a large community, and allows rapid development. It also natively supports multi-guard auth which my project requires.

**6. Explain MVC architecture in your project.**
> - **Model** — e.g., Baby.php, `Appointment.php` — handles database logic and relationships
> - **View** — Blade templates in views — handles what the user sees
> - **Controller** — e.g., GrowthRecordController.php — handles request logic, fetches data from models, passes to views
> The user sends a request → router directs it → controller processes it → model fetches data → view renders the response.

**7. What is Livewire and where did you use it?**
> Livewire is a Laravel package that allows you to build dynamic, reactive UI components without writing JavaScript. It uses AJAX behind the scenes. I used it in Livewire for dynamic UI components that update without full page reloads.

**8. What is Eloquent ORM?**
> Eloquent is Laravel's Object-Relational Mapper. Instead of writing raw SQL like `SELECT * FROM baby WHERE parent_id = 1`, you write `Baby::where('parent_id', 1)->get()`. It maps database rows to PHP objects (models), supports relationships, and prevents SQL injection by using parameter binding automatically.

**9. What database did you use and why?**
> MySQL — it's widely used, well-supported with Laravel, free, reliable for relational data, and works well with tools like phpMyAdmin which is common in Sri Lankan university and hosting environments.

---

### Authentication & Security

**10. How does Laravel's multi-guard authentication work?**
> In auth.php, each user type (admin, parent, midwife, moh, seller) is defined as a separate **guard** pointing to its own model and table. When logging in, I use `Auth::guard('parent')->attempt($credentials)` for parents, `Auth::guard('midwife')->attempt()` for midwives, etc. Each guard maintains its own session independently, so being logged in as a parent doesn't conflict with a midwife session.

**11. How are passwords stored?**
> Passwords are never stored as plain text. I use Laravel's `Hash::make($password)` which uses **bcrypt** hashing (cost factor 10 by default). When logging in, `Hash::check($input, $hashedPassword)` compares them. Even if the database is compromised, passwords cannot be reversed.

**12. What is CSRF protection?**
> Cross-Site Request Forgery is an attack where a malicious site tricks a logged-in user's browser into making requests to your site. Laravel automatically generates a unique **CSRF token** per session. Every form must include `@csrf` (the `_token` field). The `VerifyCsrfToken` middleware checks this token on every POST/PUT/DELETE request and rejects requests without a valid token.

**13. How did you implement email verification?**
> I have a `VerificationToken` model that stores a unique random token linked to the user. After registration, an email is sent with a verification link containing that token. When the user clicks the link, the token is validated and the account is marked as verified. The token is then deleted.

**14. What middleware did you use to protect routes?**
> I used custom auth middleware for each guard (e.g., `auth:parent`, `auth:midwife`, `auth:admin`). Routes in parent.php are protected so only authenticated parents can access them. Non-authenticated users are redirected to the login page.

---

### AI Growth Prediction

**15. Explain how the AI growth prediction works.**
> When a midwife or parent logs a growth record (weight, height, head circumference), the system sends this data along with the baby's age, gender, and historical records to the **Google Gemini 2.0 Flash API** via `AiGrowthPredictionService`. The API returns a structured prediction including growth trend, percentile estimate, health risks, BMI category, and recommendations. This is then saved to the `weight_records` table in the `ai_prediction` column.

**16. What happens if the Gemini API is unavailable?**
> I implemented a **fallback mechanism**. In `GrowthPredictionService`, if the API key is missing or the API call fails, it falls back to `AiGrowthPredictionService::getMockPrediction()` which generates a rule-based prediction using the baby's data locally. Errors are logged using `Log::error()`. This ensures the system never crashes just because an external API is down — this is called **graceful degradation**.

**17. What is a Laravel Queue/Job and why did you use it?**
> A Queue allows time-consuming tasks to run in the background instead of making the user wait. `GenerateGrowthPredictionJob` implements `ShouldQueue` — when a growth record is saved, instead of calling the Gemini API (which can take up to 45 seconds) during the HTTP request, the job is pushed to a queue named `'predictions'`. A queue worker processes it in the background. The user gets an instant response and the prediction appears later. `$tries = 2` means if it fails, it retries once.

**18. What growth metrics does your system track?**
> Weight, height, BMI, head circumference, developmental milestones, and the baby's age in months. These are compared against standard growth benchmarks to determine percentile and flag potential health risks.

**19. What WHO standards did you reference?**
> The WHO Child Growth Standards provide weight-for-age, height-for-age, and BMI-for-age charts. My AI prompt to Gemini instructs it to evaluate the baby's measurements against internationally recognized pediatric growth percentiles (WHO 2006 standards for 0–5 years).

---

### Real-Time Chat

**20. How does real-time chat work?**
> When a parent sends a message, `ChatController` saves it to the `chat_messages` table and fires the `ChatMessageSent` event. This event implements `ShouldBroadcast`, so Laravel pushes it through a WebSocket server (Pusher/Reverb). On the frontend, Laravel Echo listens on the private channel `chat.room.{id}` — when the event arrives, JavaScript dynamically appends the message without any page reload.

**21. What is Laravel Echo and Pusher/Reverb?**
> **Pusher/Reverb** is the WebSocket server that maintains persistent connections with browsers. **Laravel Echo** is a JavaScript library that makes it easy to subscribe to channels and listen for events. Together they enable real-time, bidirectional communication. Laravel Broadcasting connects the backend events to the WebSocket server.

**22. Why PrivateChannel instead of public?**
> A `PublicChannel` can be listened to by anyone who knows the channel name. A `PrivateChannel` requires the user to be authenticated and authorized — Laravel checks channels.php to confirm the current user belongs to that chat room before allowing them to subscribe. This prevents unauthorized users from eavesdropping on private conversations.

**23. Difference between Event and Listener?**
> An **Event** is a class that represents something that happened (e.g., `ChatMessageSent`). A **Listener** is a class that reacts to that event (e.g., sending a notification). They are loosely coupled — the code that fires the event doesn't need to know what handles it. In my chat, the event also broadcasts via WebSocket because it implements `ShouldBroadcast`.

---

### Vaccination Management

**24. Explain the vaccination tracking workflow.**
> 1. Admin/MOH creates `VaccinationSchedule` records (vaccine name, doses required, dose schedule in JSON)
> 2. Midwife views babies assigned to them and sees which vaccines are due based on the baby's age
> 3. Midwife records each dose in `BabyVaccination` table with date, batch number, side effects
> 4. `NotificationService` creates an in-app notification for the parent
> 5. A queued email (`SendVaccineScheduledEmail`) is sent to the parent confirming the vaccination

**25. How does bulk vaccination work?**
> `BulkVaccinationController` allows a midwife to record the same vaccine for multiple babies at once — useful during vaccination camps. Instead of opening each baby record individually, the midwife selects multiple babies and records the vaccination in one operation, which loops through each baby and creates `BabyVaccination` records.

**26. How are reminder emails sent?**
> `SendVaccineScheduledEmail` is a queued Job that uses `VaccineScheduledMail` (a Mailable class) to send an email to the parent. It's dispatched to the queue so it doesn't slow down the midwife's workflow. Laravel's queue worker picks it up and sends it via the configured mail driver (SMTP/Mailtrap).

**27. How does your system handle multi-dose vaccines?**
> Each `VaccinationSchedule` has a `doses_required` count and a `dose_schedule` JSON column (e.g., `{"1": 0, "2": 2, "3": 6}` — dose 1 at birth/0 months, dose 2 at 2 months, dose 3 at 6 months). The system groups existing `BabyVaccination` records by `vaccine_id` and compares with `doses_required` to determine which doses are pending.

---

### Appointments

**28. Walk through the full appointment lifecycle.**
> 1. Parent selects a baby and midwife, picks an available date/time slot — `AppointmentController@store` creates the appointment with status `pending`
> 2. `AppointmentRequestMail` is sent to the midwife
> 3. Midwife reviews and **confirms** or **rejects** via their dashboard
> 4. `AppointmentConfirmedMail` or `AppointmentRejectedMail` is sent to the parent
> 5. On confirmation, a `ChatRoom` is created linking that parent and midwife for that appointment
> 6. Both sides can chat through the linked chat room

**29. How does midwife availability work?**
> `MidwifeAvailability` stores the days/times a midwife is available. When a parent books, the system checks these slots. Before creating an appointment, it verifies no existing confirmed appointment overlaps the same midwife and time slot, preventing double-booking.

**30. Why is a chat room created on appointment confirmation?**
> It provides a dedicated, persistent communication channel tied to that specific appointment. The parent can ask pre-appointment questions and the midwife can share post-visit notes. Linking it to the appointment maintains context — you know which chat relates to which visit.

---

### Database Design

**31. Main relationships in your ER diagram.**
> - `parents` → `baby` (one parent can have many babies)
> - `midwives` → `baby` (one midwife manages many babies)
> - `baby` → `weight_records` (one baby has many growth records)
> - `baby` → `baby_vaccinations` (one baby has many vaccination records)
> - `baby_vaccinations` → `vaccination_schedules` (each record refers to one vaccine)
> - `parents` + `midwives` → `appointments` (many appointments between them)
> - `appointments` → `chat_rooms` → `chat_messages`

**32. Why does Baby have both parent_id and midwife_id?**
> A baby has one biological/legal parent (guardian) who registered them, and one assigned midwife responsible for their healthcare. These are different relationships — the parent owns the record, the midwife manages clinical care. This design allows the system to show each midwife only their assigned babies and each parent only their own children.

**33. What is database normalization? Is your DB in 3NF?**
> Normalization reduces data redundancy. 
> - **1NF** — atomic values, no repeating groups ✓
> - **2NF** — no partial dependencies (all non-key columns depend on the full primary key) ✓
> - **3NF** — no transitive dependencies (non-key columns depend only on the primary key, not on other non-key columns) ✓ — mostly, except `dose_schedule` is stored as JSON in `vaccination_schedules` which is a deliberate denormalization for flexibility.

**34. How did you use Laravel Migrations?**
> Migrations are version-controlled PHP files that define database schema changes. Running `php artisan migrate` executes them in order. Benefits: schema changes are tracked in Git, the team can sync DB structure easily, `rollback` can undo changes. I have 35+ migrations showing the evolution of my schema from initial development to the final domain-aligned structure.

**35. Give an example of Eloquent relationships from your project.**
> In Baby.php:
> ```php
> public function parentUser() {
>     return $this->belongsTo(ParentUser::class, 'parent_id');
> }
> public function midwife() {
>     return $this->belongsTo(Midwife::class, 'midwife_id');
> }
> ```
> This means I can call `$baby->parentUser->name` or `$baby->midwife->phone` without writing any SQL. Laravel handles the JOIN automatically.

---

### Notifications & Email

**36. What types of notifications does your system have?**
> Two types:
> 1. **In-app notifications** — stored in the `notifications` table via `NotificationService`, shown in the dashboard bell icon
> 2. **Email notifications** — `AppointmentConfirmedMail`, `AppointmentRejectedMail`, `AppointmentRequestMail`, `VaccineScheduledMail` — sent via Laravel's Mailable classes

**37. Why did you queue vaccine emails?**
> Sending email via SMTP is a network operation that can take 1–3 seconds or more. If done synchronously during a request, the midwife's page would freeze while waiting. By dispatching `SendVaccineScheduledEmail` to the queue, the midwife gets an instant response and the email is sent in the background by the queue worker.

**38. How does the SMS service work?**
> `SmsService.php` integrates with a local Sri Lankan SMS gateway (I can see `ESMSWS.php` and `sms_config.php` in the public folder). It sends SMS alerts — for example, when a vaccination is recorded, a text message can be sent to the parent's mobile number.

---

### Testing & Deployment

**39. Did you write tests?**
> Yes, I have `tests/Feature/` and `tests/Unit/` directories. Feature tests test full HTTP request-response cycles (e.g., does the appointment booking route work correctly?). Unit tests test individual classes/methods in isolation (e.g., does a calculation method return the correct value?). Tests are run using `php artisan test`.

**40. What is phpunit.xml for?**
> It's the PHPUnit configuration file. It defines which test directories to scan, environment variables for testing (like using an in-memory SQLite database instead of the real MySQL database during tests), and test suite organization.

**41. How would you deploy this system?**
> 1. Upload code to the server (via Git pull or FTP)
> 2. Run `composer install --no-dev`
> 3. Set up `.env` with production credentials
> 4. Run `php artisan migrate --force`
> 5. Run `php artisan config:cache` and `php artisan route:cache` for performance
> 6. Set up a queue worker as a daemon (`php artisan queue:work`) using Supervisor
> 7. Configure a web server (Apache/Nginx) to point to the `public/` directory

**42. What is the .env file? Why shouldn't it be committed to Git?**
> `.env` contains environment-specific configuration — database credentials, API keys (Gemini API key), mail passwords, app secrets. These are sensitive and differ between development and production. Committing it to Git would expose all credentials publicly if the repo is ever leaked or public. It's listed in `.gitignore`. Each developer/server has their own `.env`.

---

### Challenges & Improvements

**43. Most technically challenging part?**
> Implementing the **real-time chat system** with Laravel Broadcasting and private channels, combined with **multi-guard authentication** — ensuring the correct user type is identified in `ChatController::getCurrentUser()` and that private channel authorization works for both parents and midwives simultaneously.

**44. Limitations of the current system?**
> - No mobile app (web only)
> - SMS is dependent on a local Sri Lankan gateway
> - Growth charts are not yet rendered as visual graphs (no D3.js/Chart.js integration)
> - Queue worker must be kept running manually — needs Supervisor in production
> - AI predictions depend on Google Gemini API availability

**45. What would you add with more time?**
> - Visual growth charts (Chart.js) comparing baby's measurements against WHO percentile curves
> - A React Native / Flutter mobile app
> - Push notifications via Firebase
> - Document upload for baby medical history
> - Multi-language support (Sinhala/Tamil)

**46. How would the system scale to 10,000+ parents?**
> - Move queue processing to Redis instead of database driver
> - Use database indexing on frequently queried columns (`parent_id`, `midwife_id`, `baby_id`)
> - Add caching with `Laravel Cache` for dashboard counts
> - Use a CDN for static assets
> - Scale horizontally with load balancing

---

### FrontendController Specific

**47. Why the variable name mismatch (e.g., `$parentCount` stored as `'clients'`)?**
> This is a legacy issue from the earlier version of the project when the system was a general service marketplace with "clients" and "sellers". When the domain was pivoted to a babycare system, the database tables were renamed (e.g., `clients` → `parents`, `sellers` → `midwives`) but some frontend variable keys in this controller were not updated. In production this would be fixed — it's a technical debt item. The data is still correct, only the key names passed to the view are outdated.

---

**Tip:** If asked something you don't know, say *"I didn't implement that specific feature, but the approach would be..."* — panels respect honest acknowledgment over guessing.> public function parentUser() {
>     return $this->belongsTo(ParentUser::class, 'parent_id');
> }
> public function midwife() {
>     return $this->belongsTo(Midwife::class, 'midwife_id');
> }
> ```
> This means I can call `$baby->parentUser->name` or `$baby->midwife->phone` without writing any SQL. Laravel handles the JOIN automatically.

---

### Notifications & Email

**36. What types of notifications does your system have?**
> Two types:
> 1. **In-app notifications** — stored in the `notifications` table via `NotificationService`, shown in the dashboard bell icon
> 2. **Email notifications** — `AppointmentConfirmedMail`, `AppointmentRejectedMail`, `AppointmentRequestMail`, `VaccineScheduledMail` — sent via Laravel's Mailable classes

**37. Why did you queue vaccine emails?**
> Sending email via SMTP is a network operation that can take 1–3 seconds or more. If done synchronously during a request, the midwife's page would freeze while waiting. By dispatching `SendVaccineScheduledEmail` to the queue, the midwife gets an instant response and the email is sent in the background by the queue worker.

**38. How does the SMS service work?**
> `SmsService.php` integrates with a local Sri Lankan SMS gateway (I can see `ESMSWS.php` and `sms_config.php` in the public folder). It sends SMS alerts — for example, when a vaccination is recorded, a text message can be sent to the parent's mobile number.

---

### Testing & Deployment

**39. Did you write tests?**
> Yes, I have `tests/Feature/` and `tests/Unit/` directories. Feature tests test full HTTP request-response cycles (e.g., does the appointment booking route work correctly?). Unit tests test individual classes/methods in isolation (e.g., does a calculation method return the correct value?). Tests are run using `php artisan test`.

**40. What is phpunit.xml for?**
> It's the PHPUnit configuration file. It defines which test directories to scan, environment variables for testing (like using an in-memory SQLite database instead of the real MySQL database during tests), and test suite organization.

**41. How would you deploy this system?**
> 1. Upload code to the server (via Git pull or FTP)
> 2. Run `composer install --no-dev`
> 3. Set up `.env` with production credentials
> 4. Run `php artisan migrate --force`
> 5. Run `php artisan config:cache` and `php artisan route:cache` for performance
> 6. Set up a queue worker as a daemon (`php artisan queue:work`) using Supervisor
> 7. Configure a web server (Apache/Nginx) to point to the `public/` directory

**42. What is the .env file? Why shouldn't it be committed to Git?**
> `.env` contains environment-specific configuration — database credentials, API keys (Gemini API key), mail passwords, app secrets. These are sensitive and differ between development and production. Committing it to Git would expose all credentials publicly if the repo is ever leaked or public. It's listed in `.gitignore`. Each developer/server has their own `.env`.

---

### Challenges & Improvements

**43. Most technically challenging part?**
> Implementing the **real-time chat system** with Laravel Broadcasting and private channels, combined with **multi-guard authentication** — ensuring the correct user type is identified in `ChatController::getCurrentUser()` and that private channel authorization works for both parents and midwives simultaneously.

**44. Limitations of the current system?**
> - No mobile app (web only)
> - SMS is dependent on a local Sri Lankan gateway
> - Growth charts are not yet rendered as visual graphs (no D3.js/Chart.js integration)
> - Queue worker must be kept running manually — needs Supervisor in production
> - AI predictions depend on Google Gemini API availability

**45. What would you add with more time?**
> - Visual growth charts (Chart.js) comparing baby's measurements against WHO percentile curves
> - A React Native / Flutter mobile app
> - Push notifications via Firebase
> - Document upload for baby medical history
> - Multi-language support (Sinhala/Tamil)

**46. How would the system scale to 10,000+ parents?**
> - Move queue processing to Redis instead of database driver
> - Use database indexing on frequently queried columns (`parent_id`, `midwife_id`, `baby_id`)
> - Add caching with `Laravel Cache` for dashboard counts
> - Use a CDN for static assets
> - Scale horizontally with load balancing

---

### FrontendController Specific

**47. Why the variable name mismatch (e.g., `$parentCount` stored as `'clients'`)?**
> This is a legacy issue from the earlier version of the project when the system was a general service marketplace with "clients" and "sellers". When the domain was pivoted to a babycare system, the database tables were renamed (e.g., `clients` → `parents`, `sellers` → `midwives`) but some frontend variable keys in this controller were not updated. In production this would be fixed — it's a technical debt item. The data is still correct, only the key names passed to the view are outdated.

---

**Tip:** If asked something you don't know, say *"I didn't implement that specific feature, but the approach would be..."* — panels respect honest acknowledgment over guessing.
