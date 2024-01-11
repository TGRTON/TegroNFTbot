# TegroNFTbot - Telegram Bot

## An In-Depth Guide to Launching Your Own NFT Telegram Bot

[**Explore the Documentation »**](https://github.com/TGRTON/TegroNFTbot)

[View Demo](https://github.com/TGRTON/TegroNFTbot) |
[Report Bug](https://github.com/TGRTON/TegroNFTbot/issues) |
[Request Feature](https://github.com/TGRTON/TegroNFTbot/issues)

![Downloads](https://img.shields.io/github/downloads/TGRTON/TegroNFTbot/total)
![Contributors](https://img.shields.io/github/contributors/TGRTON/TegroNFTbot?color=dark-green)
![Issues](https://img.shields.io/github/issues/TGRTON/TegroNFTbot)
![License](https://img.shields.io/github/license/TGRTON/TegroNFTbot)

## Table Of Contents

- [About the Project](#about-the-project)
- [Built With](#built-with)
- [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [Installation](#installation)
- [Usage](#usage)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Authors](#authors)
- [Acknowledgements](#acknowledgements)

## About The Project

The TegroNFTbot is a comprehensive solution for launching a Telegram bot focused on NFT transactions. This bot facilitates interaction with an engaged audience, offering capabilities for purchasing various NFTs, participating in NFT pre-sale draws, and benefiting from a referral program.

## Built With

The bot is developed using procedural PHP version 7+, optimized for performance on any hosting supporting PHP and MySQL. This design choice simplifies the bot's code, making it easily editable and adaptable.

## Getting Started

To set up this project locally and get it running, follow these steps:

### Prerequisites

Requires hosting with PHP 7 and MySQL support.

### Installation

The main executable script of the bot is `tgbot.php`.

1) Configure `config.php` with your specific settings:
```plaintext
############################
$admin = 00000; // ChatID of manager/owner
$nftCatRate, $nftDogRate, $nftCustRate = 1; // Rates (currently disabled)
$BloggerNFT = 75; // Price in TON for BloggerNFT
$Blogger3D = 300; // Price in TON for Blogger3D
$NFTNude = 25; // Price in TON for NFTNude
$NFTRefPercent = 10; // Referral percent
$NFTwallet = "XXXXX"; // TON Wallet for payments
$toncenterAPIKey = "XXXXX"; // API Key of Toncenter
$CryptoPayAPIToken = ""; // CryptoPay API Token
define('TOKEN', 'XXXXX'); // Bot API Token
$api_key = 'XXX'; // Tegro Money API Key
$roskassa_publickey = 'XXXX'; // Tegro Money Public Key
$roskassa_secretkey = 'XXXX'; // Tegro Money Secret Key
############################
```

2) **Register the Bot in Cryptopay**
   Specify the postback URL to integrate your bot with Cryptopay for transaction processing.
   - Postback URL: [https://yourdomain/BotFolder/postback_cryptopay.php](https://yourdomain/BotFolder/postback_cryptopay.php)

3) **Set the Postback URL in Tegro Money Account**
   Configure the postback URL in your Tegro Money account to manage financial transactions efficiently.
   - Tegro Money URL: [https://yourdomain/BotFolder/postback.php](https://yourdomain/BotFolder/postback.php)

4) **Fill in MySQL Database Details**
   Update the `global.php` file with your MySQL database information to ensure proper data handling.
   - Update `global.php` with MySQL details.

5) **Import MySQL Database Structure**
   Utilize the `database.sql` file to set up your database with the required structure.
   - Import structure from `database.sql`.

6) **Install the Webhook for `tgbot.php` Script**
   Set up the webhook on Telegram's API to enable real-time interactions with your bot.
   - Webhook URL: [https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php](https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php)

7) **Edit Bot Texts in `langs.php` File**
   Customize the bot's responses and messages by editing the `langs.php` file.
   - Modify `langs.php` for custom bot texts.


### Usage Instructions

- **Finding and Starting the Bot:**
  - Search for your bot in Telegram using `@YourBot`.
  - Start the bot with the `/start` command to engage with its features.

### Roadmap and Future Developments

- **Stay Updated:** Check out the [Open Issues](https://github.com/TGRTON/TegroNFTbot/issues) section for upcoming features and known issues.

### Contributing to the Project

Your contributions shape the future of this project. Follow these guidelines to contribute:
- **Suggest Improvements:** If you have ideas or suggestions, submit them through the [Issues](https://github.com/TGRTON/TegroNFTbot/issues/new) section.
- **Quality Assurance:** Ensure your contributions are well-written and error-free.
- **Individual Pull Requests:** For each suggestion, create a separate PR.
- **Follow the Code Of Conduct:** Read and adhere to the [Code Of Conduct](https://github.com/TGRTON/TegroNFTbot/blob/main/CODE_OF_CONDUCT.md).

#### How to Create a Pull Request

1. **Fork the Repository:** Start by forking the project repository.
2. **Create a Feature Branch:** Use `git checkout -b feature/AmazingFeature` for your new feature.
3. **Commit Your Changes:** After making changes, commit them with `git commit -m 'Add some AmazingFeature'`.
4. **Push to Your Branch:** Push the changes to your feature branch using `git push origin feature/AmazingFeature`.
5. **Open a Pull Request:** Finally, open a pull request for review and integration.

### License

- The project is under the [MIT License](https://github.com/TGRTON/TegroNFTbot/blob/main/LICENSE).

### Authors and Acknowledgements

- **Primary Developer:** Lana Cool - Explore more on [Lana Cool's GitHub](https://github.com/lana4cool/).
- **Special Thanks:** Heartfelt gratitude to Lana, the key contributor and visionary behind this project.

