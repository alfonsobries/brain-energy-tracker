# 🧠⚡️ Brain Energy Tracker with ChatGPT and Telegram

This self-hosted app uses Telegram to gather daily meal details and feelings from users, analyzing this data with ChatGPT's API to estimate the nutritional content.

## Features

*Daily Logging via Telegram:* Each day, the app sends messages through Telegram asking about your meals and how you feel. These questions are designed to gather detailed information about what you ate during the day.

*ChatGPT-Powered Analysis:* Utilizing your responses sent via Telegram, the app employs the ChatGPT API to analyze and estimate the nutritional content of your daily diet. This includes assessing the amounts of carbohydrates, sugars, and other key nutritional elements of your meals.

*Personal Data Storage:* The app is currently focused on securely storing all collected data in a database. This information, accessible to you, can be used at your discretion. Plans are in place to enhance the app with features for structured data visualization, such as tables, and capabilities to automatically identify dietary patterns in the future.

In its current form, the app acts as a sophisticated tool for data collection and personal health monitoring, leveraging Telegram for convenient daily interactions and ChatGPT's advanced analytics for nutritional insights.

## Configuration

#### 1. Initial Steps

1. Clone this repository: `git clone alfonsobries/&`
2. The app is built on Laravel. Visit [Laravel documentation](https://laravel.com/docs) for instructions.
3. This app only requires a single user; you can optionally add the user details to your `.env` file:

```
ADMIN_EMAIL=alfonso@example.com
ADMIN_PASSWORD=my-password
ADMIN_NAME=Alfonso
```

4. Run a fresh migration: `php artisan migrate:fresh --seed`

#### 2. Configure Telegram

1. On Telegram: Start a conversation with BotFather by searching for @BotFather.
2. Create a new bot using the `/newbot` command and follow the instructions.
3. Assign a name, such as `'mytracker9000_bot'`. It must end with '_bot'.
4. Receive the token, which will look similar to `1234567890:AAG2eDLduCRjsgHlms1EezWoCqBlpsSJexE`.
5. Add the token to your `.env` file: `TELEGRAM_BOT_TOKEN={your_token}`

#### 3. Configure ChatGPT

1. Obtain a new API key from [OpenAI](https://openai.com/).
2. Add the API key to your `.env` file: `OPENAI_KEY=sk-12345`

#### 4. Create an Initial User

1. Use the same steps as in section 3 for obtaining and adding the OpenAI API key.

#### 5. Configure the Telegram Webhook

1. Ensure the `APP_URL` in your .env file is publicly accessible (if testing locally, you can use a tool like [expose](https://expose.dev/)).
2. Run `php artisan telegram:set-webhook` in the terminal; you should see a success message.
3. Then, run `php artisan telegram:code` in the terminal; you should receive a command like the one shown below.

```
Telegram command is:

/start bcJpdiI6InlCbUo0USthMStXVUNCbmxGVFVKTUE9PSIsInZhbHVlIjoiWFZwbnFTN1QvQ1BObDU4VG01ZTZhRlJqeUJudFVIciswbUZNYVJpaHdXWT0iLCJtYWMiOiJmNjdlYTZhNTE2M2JiNmFjZTdkMDhmNWIzYzkzMWFjYTY5YzRtMmU2NzQyNmJlMzIyYjI2NTk3ODJhZjc2MzcxIiwidGFnIjoiIn0
```

4. Copy the command starting with `/start`.
5. Search for your bot on Telegram and send the command as a message.
6. If everything goes well, you should receive a success message from the Telegram bot.
