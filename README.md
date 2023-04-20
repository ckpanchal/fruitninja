# Fruitninja Installation

1. Clone repository using this command:
	`git clone https://github.com/ckpanchal/fruitninja.git`

2. Install required dependencies using this commad:
	`composer install`

3. Update `DATABASE_URL` and `MAILER_DSN` params in .env file. Currently it contains my data.

4. Create database using this command:
	`php bin/console doctrine:database:create`

5. Create database tables using migration command:
	`php bin/console doctrine:migrations:migrate`

6. Now run this command to fetch & seed fruits in database
	`php bin/console fetch:fruits`

Now there are two ways to start web server. Using symfony CLI or PHP web server

## If you have installed symfony CLI then you can run below commands to start and stop server.

	`symfony server:start --d`
	`symfony server:stop`

## If you want to start web server using php then run below command

	`php -S localhost:8000 -t public/`
