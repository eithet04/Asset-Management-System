# Axora asset management

Laravel application for maintaining an asset register. The admin asset registry
supports persistent asset registration, search, status filtering, and summary
counts. Asset registrations are recorded in the configured database and in the
activity log. The admin **Manage your workspace** page provides category and
location create/edit/archive forms, account role and activation management,
maintenance tracking with asset status history, and persisted company settings.

## Requirements

- PHP 8.3 or later with the extensions required by Laravel
- Composer
- Node.js and npm
- PostgreSQL

## Local setup

Run these commands from the `asset-management-system` directory:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
npm ci
npm run build
php artisan serve
```

The `.env.example` uses PostgreSQL. Create the database and set its connection
values in the ignored `.env` file before migrating. The seeder creates
application roles and starter asset categories; it does not create a shared
demo user. Register with a Gmail address and Myanmar mobile number, verify the
address, then promote the account to an administrator:

```powershell
php artisan users:assign-role your-email@gmail.com admin
```

Each teammate using a separate local database must create their own administrator
account after migrating. From the `asset-management-system` directory, run:

```powershell
php artisan users:create-admin your-admin@gmail.com
```

The command prompts for the administrator name and a strong password without
echoing the password. Do not commit database dumps, account passwords, or
`.env` credentials to Git. For a shared team login, deploy one application
connected to a shared, access-controlled database instead of copying account
credentials into the repository.

## Login, Gmail verification, and two-step sign-in

Email login accepts Gmail addresses only. New employee accounts and all
non-admin roles must verify their Gmail address. Admins do not need email
verification or a sign-in code; asset controllers skip the sign-in code but
still need a verified Gmail address. Other logins (including phone-number
logins) require a six-digit email code. Myanmar phone numbers are stored in
normalized `+959...` form.
Passwords require at least eight characters with uppercase, lowercase, a
number, and a symbol. Password reset links use Laravel's `password_reset_tokens`
table and expire after the configured 60 minutes.

To send verification links, sign-in codes, and password reset messages through
Gmail, use a Google App Password (not your regular Gmail password) and set these
values in `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-account@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_FROM_ADDRESS=your-account@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Keep the app password private and never commit `.env`. After changing mail or
database settings when configuration is cached, run `php artisan config:clear`.
For a development-only mail preview, Laravel's `MAIL_MAILER=log` writes
messages to the application log instead of delivering them.

The authentication schema migration adds a nullable unique phone field and
the OTP challenge table without changing existing user data. Existing users
can continue using a verified Gmail address; administrators can add their
normalized phone number on Admin → User roles to enable phone login.

Available roles are `admin`, `asset_controller`, `manager`, `department_manager`,
`hr_admin_staff`, `it_technician`, `management_viewer`, and `employee`.
Assign a department to department-manager accounts on the Admin → User roles
page; they cannot access department data until this is set. HR/Admin Staff get a
read-only employee asset list, IT Staff/Technicians get maintenance and
condition workflows, and Management/Viewers get read-only reports.

## Sharing live records with teammates

Git push/pull shares application code, not records stored in a local database.
The simplest team setup is one deployed website connected to a private hosted
database; teammates sign in to that website, and do not need to run or pull the
app to see new records. If you also need multiple app installations to share
records:

1. Create a hosted MySQL/MariaDB database and a restricted database user.
2. In each installation's ignored `.env`, set `DB_CONNECTION=mysql` and the
   same database connection details. Share credentials only through a secure
   secret channel, use TLS, and restrict database network access to trusted
   hosts; do not expose the database to the public internet.
3. Deploy or pull the application code, then run `php artisan migrate --seed`
   against that shared database.
4. Each teammate signs in with their own account. Do not copy `.env` or
   production database credentials into Git.

The shared database is the source of truth: new records appear for everyone
using that database without another code push. Keep regular database backups
and do not use real employee or company data as Git seed files. Local SQLite
databases remain private to each machine.

The admin management sections are separate pages linked from the dashboard:
`/admin/categories`, `/admin/locations`, `/admin/branches`, `/admin/departments`,
`/admin/users`, `/admin/maintenance`, and `/admin/settings`. Categories and
locations can be deactivated rather than deleted, so previous asset references
and history remain intact; inactive categories and locations cannot be selected
for new assets. Location codes are not required. User access and organization
management require an active administrator account.

## Asset lifecycle

After pulling these changes, apply the additive asset operations migrations from
the `asset-management-system` directory:

```powershell
php artisan migrate
```

Do not use `migrate:fresh` on a database with records. Admins and managers can
open `/operations/assignments`, `/operations/returns`, `/operations/transfers`,
`/operations/status`, `/operations/requests`, `/operations/history`, and
`/operations/reports` from the asset operations navigation. Purchase records
and invoice attachments are at `/operations/purchases`; warranty details,
documents, and claims are at `/operations/warranties`; inventory check findings
are at `/operations/verification`; disposal requests and approvals are at
`/operations/disposals`; maintenance with photo attachments and technician
updates is at `/operations/maintenance`; and each signed-in user can read
their notifications at `/operations/notifications` (employees use
`/employee/notifications`). These pages record
who performed each action and keep asset assignments, returns, transfers,
status/condition changes, and activity history in the configured database.
Employee accounts can submit a request at `/employee/asset-requests` and track
its approval there. Newly registered employee accounts receive a linked
employee profile so they can be assigned assets. Existing employee accounts
receive a profile the next time they open their employee dashboard.

Reports can be downloaded as CSV. Use a private shared database (rather than
Git) if teammates need to see the same live records.
Warranty expiry reminders are scheduled daily at 08:00 Asia/Yangon time for
30, 7, and 1 day before expiry. On a local Windows development machine, run
`php artisan schedule:work` in a separate terminal to process the reminders;
production deployments should run Laravel's scheduler once per minute.
Uploaded invoices, warranty documents, and maintenance photos are stored on
Laravel's private local disk and downloaded only by authenticated managers or
administrators.

## Development

```powershell
php artisan test
npm run dev
```
