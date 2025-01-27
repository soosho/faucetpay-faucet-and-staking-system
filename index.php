<?php
session_start();
// Check if a referral link is used (i.e., ref ID is present in the URL)
if (isset($_GET['ref'])) {
    // Store the referrer in the session for future use
    $_SESSION['referrer_id'] = $_GET['ref'];
}

require 'includes/db.php';
require 'config/config.php'; // Ensure config file is loaded

// Query to get total users from the 'users' table
$stmt_users = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmt_users->execute();
$total_users = $stmt_users->fetchColumn();

// Query to get total withdrawals from the 'withdraw_requests' table
$stmt_withdrawals = $pdo->prepare("SELECT COUNT(*) AS total_withdrawals FROM withdraw_requests");
$stmt_withdrawals->execute();
$total_withdrawals = $stmt_withdrawals->fetchColumn();

// Query to get total deposits from the 'deposit_requests' table
$stmt_deposits = $pdo->prepare("SELECT COUNT(*) AS total_deposits FROM deposit_requests");
$stmt_deposits->execute();
$total_deposits = $stmt_deposits->fetchColumn();
?>

<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="/">
    <meta charset="utf-8">
    <meta name="author" content="Melons">
    <meta name="Trafficstars" content="63092">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Earn passive income through cryptocurrency staking and daily faucet claims with FaucetMineHub.com. Seamless payouts via FaucetPay.">
    <meta name="keywords" content="FaucetMineHub, staking crypto, crypto faucet, earn free crypto, daily crypto rewards, cryptocurrency staking, faucet claims, crypto rewards, free Bitcoin, free Ethereum, free Dogecoin, free Litecoin, FaucetPay withdrawals, crypto staking rewards, passive crypto income, instant crypto withdrawals, referral earnings, Bitcoin staking, Ethereum staking, Dogecoin faucet, Litecoin faucet, Tron staking, Tether TRC20, Binance Coin staking, earn free cryptocurrency, crypto earnings, staking plans, daily faucet claims, crypto investment, supported cryptocurrencies, Bitcoin, Ethereum, Dogecoin, Litecoin, Bitcoin Cash, Dash, DigiByte, Tron, USDT, Feyorra, Zcash, BNB, Solana, Ripple, Polygon, MATIC, Cardano, TON, Stellar, XLM, USD Coin, Monero, Taraxa, crypto wallet, crypto passive income, crypto referrals, faucetpay faucet">
    <!-- Fav Icon  -->
    <meta name="google-adsense-account" content="ca-pub-3060302300389997">
    <link rel="shortcut icon" href="./images/favicon2.png">
    <!-- Page Title  -->
    <title>FaucetMineHub.com | Earn Free Crypto with FaucetPay Faucet & Crypto Faucets</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="home/css/dashlite.css">
    <link id="skin-default" rel="stylesheet" href="home/css/theme.css">
    <script type="text/javascript" src="https://app.web3ads.net/main.js" async></script>
    <meta property="og:title" content="FaucetMineHub - Earn Free Crypto with the Best FaucetPay Faucet">
<meta property="og:description" content="Join FaucetMineHub, the best FaucetPay faucet to earn free Bitcoin, Ethereum, and other cryptocurrencies with daily rewards and staking opportunities.">
<meta property="og:image" content="faucetminehub.jpg">
<meta property="og:url" content="https://www.faucetminehub.com/">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="FaucetMineHub - Earn Free Crypto with the Best FaucetPay Faucet">
<meta name="twitter:description" content="Earn free crypto through FaucetMineHub, a leading FaucetPay faucet with daily claims and staking rewards.">
<meta name="twitter:image" content="faucetminehub.jpg">
<link rel="canonical" href="https://www.faucetminehub.com/">
    <!-- TrustBox script -->
<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
<!-- End TrustBox script -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F4ERE043ZG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F4ERE043ZG');
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "FaucetMineHub",
    "url": "https://faucetminehub.com",
    "description": "Earn passive income through cryptocurrency staking and daily claims with FaucetMineHub.com, a leading FaucetPay faucet.",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "https://faucetminehub.com/search?query={search_term_string}",
        "query-input": "required name=search_term_string"
    },
    "sameAs": [
        "https://www.facebook.com/FaucetMineHub",
        "https://twitter.com/FaucetMineHub"
    ]
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com",
            "url": "https://faucetminehub.com",
            "name": "FaucetMineHub - Home",
            "description": "Earn passive income through cryptocurrency staking and daily claims with FaucetMineHub.com, a leading FaucetPay faucet."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/login",
            "url": "https://faucetminehub.com/login",
            "name": "FaucetMineHub - Login",
            "description": "Login to FaucetMineHub to access your account and start earning cryptocurrency."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/register",
            "url": "https://faucetminehub.com/register",
            "name": "FaucetMineHub - Register",
            "description": "Register on FaucetMineHub to start earning cryptocurrency through staking and daily claims."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/statistic",
            "url": "https://faucetminehub.com/statistic",
            "name": "FaucetMineHub - Statistics",
            "description": "View statistics of earnings, claims, and staking rewards on FaucetMineHub."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/levels",
            "url": "https://faucetminehub.com/docs/levels",
            "name": "FaucetMineHub - Levels",
            "description": "Learn about the level system on FaucetMineHub and the benefits of leveling up."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/chat",
            "url": "https://faucetminehub.com/docs/chat",
            "name": "FaucetMineHub - Chat",
            "description": "Join the community chat on FaucetMineHub to discuss strategies and earn together."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/daily",
            "url": "https://faucetminehub.com/docs/daily",
            "name": "FaucetMineHub - Daily Claims",
            "description": "Learn how to maximize your earnings with daily claims on FaucetMineHub."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/staking",
            "url": "https://faucetminehub.com/docs/staking",
            "name": "FaucetMineHub - Staking",
            "description": "Find out how to earn rewards through staking on FaucetMineHub."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/deposit",
            "url": "https://faucetminehub.com/docs/deposit",
            "name": "FaucetMineHub - Deposit",
            "description": "Learn how to deposit funds into your FaucetMineHub account for staking."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/withdraw",
            "url": "https://faucetminehub.com/docs/withdraw",
            "name": "FaucetMineHub - Withdraw",
            "description": "Find out how to withdraw your earnings from FaucetMineHub to your wallet."
        },
        {
            "@type": "WebPage",
            "@id": "https://faucetminehub.com/docs/referrals",
            "url": "https://faucetminehub.com/docs/referrals",
            "name": "FaucetMineHub - Referrals",
            "description": "Learn how to earn extra rewards by referring friends to FaucetMineHub."
        }
    ]
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What is FaucetMineHub?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "FaucetMineHub is a platform where users can earn free cryptocurrency through daily claims, staking, and referral rewards. It also supports seamless payouts through FaucetPay."
            }
        },
        {
            "@type": "Question",
            "name": "How do I earn crypto with FaucetMineHub?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "You can earn crypto by making daily claims, participating in staking programs, and referring new users to the platform. Each activity gives you rewards in various supported cryptocurrencies like Bitcoin, Ethereum, Dogecoin, and more."
            }
        },
        {
            "@type": "Question",
            "name": "What is FaucetPay?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "FaucetPay is a micro-wallet service that allows users to receive small amounts of cryptocurrency from different faucets, such as FaucetMineHub. It facilitates quick and low-fee withdrawals to your main wallet."
            }
        },
        {
            "@type": "Question",
            "name": "How do I withdraw my earnings from FaucetMineHub?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "You can withdraw your earnings by navigating to the 'Withdraw' section on FaucetMineHub. Select your preferred cryptocurrency and the amount you wish to withdraw, then follow the prompts to complete the transaction through FaucetPay."
            }
        },
        {
            "@type": "Question",
            "name": "Is there a minimum withdrawal amount?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes, each cryptocurrency has a specific minimum withdrawal amount. You can view the minimums on the 'Withdraw' page of FaucetMineHub. Ensure you meet the minimum before attempting a withdrawal."
            }
        },
        {
            "@type": "Question",
            "name": "How does the referral program work?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "The referral program allows you to earn a percentage of your referred users' earnings. Simply share your unique referral link with friends, and once they sign up and start earning, you'll receive a bonus on their activity."
            }
        },
        {
            "@type": "Question",
            "name": "How can I contact support?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "You can contact FaucetMineHub's support by emailing cs@faucetminehub.com or by using the contact form available on the support page."
            }
        },
        {
            "@type": "Question",
            "name": "Is FaucetMineHub free to use?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes, FaucetMineHub is completely free to use. You can start earning cryptocurrency without any upfront costs by making daily claims and participating in our staking and referral programs."
            }
        }
    ]
}
</script>

</head>

<body class="nk-body bg-white npc-landing ">

    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <header class="header has-header-main-s1 bg-grad-a mb-5 mb-sm-6 mb-md-7" id="home">
                <div class="header-main header-main-s1 is-sticky is-transparent on-dark">
                    <div class="container header-container">
                        <div class="header-wrap">
                            <div class="header-logo">
                                <a href="/" class="logo-link">
                                    <img class="logo-light logo-img" src="./images/newlogo-white.png" srcset="./images/newlogo-white.png" alt="logo">
                                    <img class="logo-dark logo-img" src="./images/newlogo.png" srcset="./images/newlogo.png" alt="logo-dark">
                                </a>
                            </div>
                            <div class="header-toggle">
                                <button class="menu-toggler" data-target="mainNav">
                                    <em class="menu-on icon ni ni-menu"></em>
                                    <em class="menu-off icon ni ni-cross"></em>
                                </button>
                            </div><!-- .header-nav-toggle -->
                            <nav class="header-menu" data-content="mainNav">
                                <ul class="menu-list ms-lg-auto">
                                    </li>
                                    <li class="menu-item"><a href="#home" class="menu-link nav-link">Home</a></li>
                                    <li class="menu-item"><a href="#service" class="menu-link nav-link">Service</a></li>
                                    <li class="menu-item"><a href="statistic" class="menu-link nav-link">Transaction History</a></li>
                                    <li class="menu-item has-sub">
                                        <a href="#" class="menu-link menu-toggle">How it's Works?</a>
                                        <div class="menu-sub">
                                            <ul class="menu-list">
                                                <li class="menu-item"><a href="docs/levels" target="_blank" class="menu-link">Levels</a></li>
                                                <li class="menu-item"><a href="docs/chat" target="_blank" class="menu-link">Chat</a></li>
                                                <li class="menu-item"><a href="docs/daily" target="_blank" class="menu-link">Daily</a></li>
                                                <li class="menu-item"><a href="docs/staking" target="_blank" class="menu-link">Staking</a></li>
                                                <li class="menu-item"><a href="docs/deposit" target="_blank" class="menu-link">Deposit</a></li>
                                                <li class="menu-item"><a href="docs/withdraw" target="_blank" class="menu-link">Withdraw</a></li>
                                                <li class="menu-item"><a href="docs/referrals" target="_blank" class="menu-link">Referrals</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="menu-item has-sub">
                                        <a href="#" class="menu-link menu-toggle">Group</a>
                                        <div class="menu-sub">
                                            <ul class="menu-list">
                                                <li class="menu-item"><a href="https://discord.gg/h5XQuq3wPN" target="_blank" class="menu-link">Discord Server</a></li>
                                                <li class="menu-item"><a href="https://t.me/faucetminehub" target="_blank" class="menu-link">Telegram</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="menu-item"><a href="https://faucetminehub.com/read/" target="_blank" class="menu-link nav-link">Blog</a></li>
                                </ul>
                                <ul class="menu-btns">
                                    <li>
                                        <a href="dashboard" target="_blank" class="btn btn-primary btn-lg">Dashboard</a>
                                    </li>
                                </ul>
                            </nav><!-- .nk-nav-menu -->
                        </div><!-- .header-warp-->
                    </div><!-- .container-->
                </div><!-- .header-main-->
                <div class="header-content my-auto py-5 is-dark">
                    <div class="container">
                        <div class="row justify-content-center text-center">
                            <div class="col-lg-7 col-md-10">
                                <div class="header-caption">
                                    <h1 class="header-title">Earn Crypto Effortlessly</h1>
                                    <div class="header-text">
                                        <p>With FaucetMineHub’s Staking System, you can effortlessly grow your cryptocurrency holdings. Stake multiple coins, track real-time earnings, and enjoy automatic rewards—all with full transparency and security. Start staking today and watch your crypto grow!</p>
                                    </div>
                                    <ul class="header-action btns-inline">
                                        <li><a href="login" class="btn btn-primary btn-lg"><span>login</span></a></li>
                                        <li><a href="register" class="btn btn-success btn-lg"><span>register</span></a></li>
                                    </ul>
                                    <br>
                                
<center><iframe id='banner_advert_wrapper_6625' src='https://api.fpadserver.com/banner?id=6625&size=250x250' width='250px' height='250px' frameborder='0'></iframe></center>
<!-- End TrustBox widget --></center>
                                </div><!-- .header-caption -->
                            </div><!-- .col -->
                        </div><!-- .row -->
                    </div><!-- .container -->
                </div><!-- .header-content -->
                <div class="header-image mb-n5 mb-sm-n6 mb-md-n7">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <div class="card overflow-hidden p-2 bg-light">
                                    <img src="dashboard.png" alt="faucetminehub">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header><!-- .header -->
            <section class="section section-service" id="service">
                <div class="container">
                    <div class="row">
                        <div class="col-10 col-sm-7 col-md-5">
                            <div class="section-head">
                                <h2 class="title">We provide various kind of service for you</h2>
                            </div><!-- .section-head -->
                        </div><!-- .col -->
                    </div><!-- .row -->
                    <div class="section-content">
                        <div class="row g-gs">
                            <div class="col-sm-6 col-lg-4">
                                <div class="service service-s1">
                                    <div class="service-icon styled-icon styled-icon-s1">
                                        <svg x="0px" y="0px" viewBox="0 0 512 512" style="fill:currentColor" xml:space="preserve">
                                            <path d="M488.4,492h-21.9V173.5c0-14.8-12.1-26.9-26.9-26.9h-49c-14.8,0-26.9,12.1-26.9,26.9V492H308V317.8
		c0-14.8-12.1-26.9-26.9-26.9h-49c-14.8,0-26.9,12.1-26.9,26.9V492h-55.7v-90.2c0-14.8-12.1-26.9-26.9-26.9h-49
		c-14.8,0-26.9,12.1-26.9,26.9V492H23.6c-5.5,0-10,4.5-10,10s4.5,10,10,10h464.8c5.5,0,10-4.5,10-10S493.9,492,488.4,492L488.4,492z
		M129.5,492H66.7v-90.2c0-3.8,3.1-6.9,6.9-6.9h49c3.8,0,6.9,3.1,6.9,6.9L129.5,492z M288,492h-62.8V317.8c0-3.8,3.1-6.9,6.9-6.9h49
		c3.8,0,6.9,3.1,6.9,6.9V492z M446.5,492h-62.8V173.5c0-3.8,3.1-6.9,6.9-6.9h49c3.8,0,6.9,3.1,6.9,6.9L446.5,492z" />
                                            <path d="M466.4,10.5c0.1-2.7-0.8-5.5-2.9-7.6s-4.9-3-7.6-2.9c-0.2,0-0.3,0-0.5,0H395c-5.5,0-10,4.5-10,10s4.5,10,10,10h37.4
		l-98.9,98.9l-37.3-37.3c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2,1.1-7.1,2.9L102.3,261.3c-3.9,3.9-3.9,10.2,0,14.1c2,2,4.5,2.9,7.1,2.9
		s5.1-1,7.1-2.9l172.7-172.7l37.3,37.3c3.9,3.9,10.2,3.9,14.1,0L446.5,34.1V68c0,5.5,4.5,10,10,10s10-4.5,10-10V11
		C466.5,10.8,466.4,10.7,466.4,10.5L466.4,10.5z" />
                                            <circle cx="75.6" cy="303.3" r="10" />
                                        </svg>
                                    </div>
                                    <div class="service-text">
                                        <h4 class="title">Real-Time Earnings</h4>
                                        <p>Staked balances and earned rewards are updated in real-time, allowing users to track their earnings instantly.</p>
                                    </div>
                                </div><!-- .service -->
                            </div><!-- .col -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="service service-s1">
                                    <div class="service-icon styled-icon styled-icon-s1">
                                        <svg viewBox="0 0 512 512" style="fill:currentColor" xml:space="preserve">
                                            <g>
                                                <path d="M462.5,276.9V115c28-4,49.5-28.1,49.5-57.1C512,25.9,486.1,0,454.2,0s-57.8,25.9-57.8,57.8H0v330.3h248.2
		c4.3,69,61.6,123.9,131.7,123.9c72.9,0,132.1-59.3,132.1-132.1C512,338.3,492.6,301.1,462.5,276.9z M454.2,16.5
		c22.8,0,41.3,18.5,41.3,41.3S477,99.1,454.2,99.1s-41.3-18.5-41.3-41.3S431.4,16.5,454.2,16.5z M16.5,74.3h382.3
		c2,6.6,5.1,12.7,9.1,18l-50.5,50.5c-3.2-1.6-6.8-2.5-10.6-2.5s-7.4,0.9-10.6,2.5l-16.6-16.6c1.5-3.2,2.5-6.8,2.5-10.6
		c0-13.7-11.1-24.8-24.8-24.8c-13.7,0-24.8,11.1-24.8,24.8c0,3.8,0.9,7.4,2.5,10.6l-41.4,41.4c-3.2-1.5-6.8-2.5-10.6-2.5
		c-13.7,0-24.8,11.1-24.8,24.8s11.1,24.8,24.8,24.8s24.8-11.1,24.8-24.8c0-3.8-0.9-7.4-2.5-10.6l41.4-41.4c3.2,1.5,6.8,2.5,10.6,2.5
		s7.4-0.9,10.6-2.5l16.6,16.6c-1.5,3.2-2.5,6.8-2.5,10.6c0,13.7,11.1,24.8,24.8,24.8c13.7,0,24.8-11.1,24.8-24.8
		c0-3.8-0.9-7.4-2.5-10.6l50.5-50.5c7.6,5.7,16.5,9.5,26.3,10.9v150.7c-19.5-11.3-42-17.9-66.1-17.9c-39.5,0-74.9,17.5-99.1,45V256
		h-49.5v99.1h18.9c-1,5.4-1.6,10.9-2,16.5H16.5V74.3z M346.8,156.9c4.6,0,8.3,3.7,8.3,8.3c0,4.6-3.7,8.3-8.3,8.3
		c-4.6,0-8.3-3.7-8.3-8.3C338.6,160.6,342.3,156.9,346.8,156.9z M289,115.6c0-4.6,3.7-8.3,8.3-8.3c4.6,0,8.3,3.7,8.3,8.3
		c0,4.6-3.7,8.3-8.3,8.3C292.7,123.9,289,120.2,289,115.6z M231.2,189.9c0,4.6-3.7,8.3-8.3,8.3s-8.3-3.7-8.3-8.3s3.7-8.3,8.3-8.3
		S231.2,185.4,231.2,189.9z M379.9,445.9c-36.4,0-66.1-29.6-66.1-66.1s29.6-66.1,66.1-66.1s66.1,29.6,66.1,66.1
		S416.3,445.9,379.9,445.9z M264.3,316c-4,7.1-7.2,14.7-9.8,22.5h-6.7v-66.1h16.5V316z M371.6,264.7v33
		c-41.7,4.2-74.3,39.4-74.3,82.2c0,12.1,2.7,23.5,7.4,33.9L276,430.3c-7.4-15.3-11.7-32.3-11.7-50.4
		C264.3,318.9,311.7,268.9,371.6,264.7L371.6,264.7z M379.9,495.5c-39.8,0-74.9-20.2-95.7-50.8l28.8-16.6
		c15,20.8,39.4,34.4,66.9,34.4s51.9-13.6,66.9-34.4l28.8,16.6C454.8,475.3,419.6,495.5,379.9,495.5z M483.7,430.3l-28.6-16.5
		c4.7-10.4,7.4-21.8,7.4-33.9c0-42.7-32.7-78-74.3-82.2v-33c59.9,4.3,107.4,54.2,107.4,115.2C495.5,398,491.2,415,483.7,430.3
		L483.7,430.3z" />
                                                <path d="M379.9,330.3c-27.3,0-49.5,22.2-49.5,49.5c0,27.3,22.2,49.5,49.5,49.5s49.5-22.2,49.5-49.5
		C429.4,352.5,407.2,330.3,379.9,330.3z M379.9,412.9c-18.2,0-33-14.8-33-33c0-18.2,14.8-33,33-33c18.2,0,33,14.8,33,33
		C412.9,398.1,398.1,412.9,379.9,412.9z" />
                                                <path d="M33,90.8h16.5v16.5H33V90.8z" />
                                                <path d="M66.1,90.8h115.6v16.5H66.1V90.8z" />
                                                <path d="M33,123.9h16.5v16.5H33V123.9z" />
                                                <path d="M66.1,123.9h115.6v16.5H66.1V123.9z" />
                                                <path d="M33,156.9h16.5v16.5H33V156.9z" />
                                                <path d="M66.1,156.9h115.6v16.5H66.1V156.9z" />
                                                <path d="M33,189.9h16.5v16.5H33V189.9z" />
                                                <path d="M66.1,189.9h115.6v16.5H66.1V189.9z" />
                                                <path d="M33,355.1h49.5v-82.6H33V355.1z M49.5,289h16.5v49.5H49.5V289z" />
                                                <path d="M99.1,355.1h49.5V289H99.1V355.1z M115.6,305.5h16.5v33h-16.5V305.5z" />
                                                <path d="M165.2,355.1h49.5V223h-49.5V355.1z M181.7,239.5h16.5v99.1h-16.5V239.5z" />
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="service-text">
                                        <h4 class="title">Multi-Currency Support</h4>
                                        <p>Users can stake multiple cryptocurrencies like Bitcoin (BTC), Ethereum (ETH), Dogecoin (DOGE), and more. We support all of FaucetPay coins!</p>
                                    </div>
                                </div><!-- .service -->
                            </div><!-- .col- -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="service service-s1">
                                    <div class="service-icon styled-icon styled-icon-s1">
                                        <svg x="0px" y="0px" viewBox="0 0 512 512" style="fill:currentColor" xml:space="preserve">
                                            <g>
                                                <g>
                                                    <g>
                                                        <path d="M472,236.7c3.7-10.2,5.7-21,5.6-31.9c-0.1-42-28.1-78.9-68.5-90.3C406.5,49.5,352.3-1.3,287.4,0S170.5,54.5,170.5,119.4
				c0,3.4,0.2,6.8,0.5,10.2c-27.5-5.3-55.9,1.9-77.5,19.8s-34,44.4-34,72.4c0,3,0.2,6.1,0.5,9.1c-35.7,4.4-61.9,35.9-59.7,71.8
				s31.8,64,67.8,64.2H135c11.3,40.7,40.7,100.9,116.6,144c2.6,1.5,5.8,1.5,8.4,0c75.9-43.1,105.3-103.3,116.6-144h66.9
				c32.1,0,59.9-22.3,66.7-53.6C517.1,282,501.2,250.1,472,236.7z M366.8,322.7c-0.4,10.9-1.9,21.7-4.6,32.2
				c-0.3,0.8-0.5,1.6-0.6,2.4c-9.2,36.5-34.7,94.4-105.8,136.4c-71.1-42-96.6-99.8-105.8-136.4c-0.1-0.8-0.3-1.6-0.6-2.4
				c-2.6-10.5-4.2-21.3-4.5-32.2V213.3c0-4.7,3.8-8.5,8.5-8.5h204.8c4.7,0,8.5,3.8,8.5,8.5L366.8,322.7L366.8,322.7z M479.8,334.8
				L479.8,334.8c-9.6,9.7-22.6,15.1-36.2,15h-62.9c1.8-9,2.9-18.1,3.1-27.2V213.3c0-14.1-11.5-25.6-25.6-25.6H153.4
				c-14.1,0-25.6,11.5-25.6,25.6v109.3c0.2,9.1,1.3,18.2,3.1,27.2H68.1c-27.3,0-49.8-21.4-51.1-48.7c-1.3-27.3,19-50.8,46.2-53.4
				c4.6,16.3,13.6,31,26,42.5c2.2,2.2,5.4,3,8.4,2.1c3-0.9,5.3-3.3,5.9-6.3c0.7-3-0.3-6.2-2.7-8.3c-11.6-10.8-19.5-25-22.6-40.6
				c-5.5-27,3.9-54.9,24.6-73.1s49.6-23.9,75.6-15c0.2,0,0.3,0.1,0.5,0.1c0.6,0.1,1.1,0.2,1.7,0.2c0.5,0.1,1.1,0.1,1.6,0
				c0.2,0,0.4,0,0.5,0c0.4-0.1,0.7-0.3,1-0.4c0.5-0.2,1.1-0.4,1.6-0.6c0.5-0.3,0.9-0.6,1.3-1c0.9-0.7,1.6-1.6,2-2.6
				c0.2-0.3,0.4-0.6,0.5-0.9c0.1-0.2,0-0.3,0.1-0.5c0.1-0.6,0.2-1.1,0.2-1.7c0.1-0.6,0.1-1.1,0-1.7c0-0.2,0-0.3,0-0.5
				c-1.3-6.6-1.9-13.3-2-20c0.1-36.6,19.7-70.3,51.4-88.5s70.8-18.1,102.4,0.3s51.1,52.2,51,88.8c0,0.4-0.1,0.7-0.1,1.1
				c-0.1,15-3.6,29.7-10.2,43.2c-2.1,4.2-0.3,9.4,3.9,11.4c4.2,2.1,9.4,0.3,11.4-3.9c6-12.4,9.9-25.8,11.2-39.6
				c31,10.6,51.9,39.8,52,72.6c0,11.1-2.4,22-7,32.1c-0.2,0.3-0.4,0.6-0.5,1c-7,14.9-18.7,27.1-33.2,34.8c-3.4,1.9-5.2,5.8-4.2,9.6
				c1,3.8,4.4,6.5,8.3,6.5c1.4,0,2.8-0.3,4-1c15.3-8.2,27.9-20.6,36.6-35.6c15.3,6.9,26.3,20.9,29.4,37.4
				C496.9,306,491.6,322.9,479.8,334.8z" />
                                                        <path d="M332.6,221.8c-4.7,0-8.5,3.8-8.5,8.5s3.8,8.5,8.5,8.5v83.6c-0.1,3.2-2.2,79.5-81.6,133.7c-2.6,1.7-4.1,4.6-3.9,7.7
				c0.2,3.1,2.1,5.8,4.9,7.2s6.1,1,8.6-0.8c86.7-59,89-143.9,89.1-147.7v-83.7C349.7,229.5,342,221.8,332.6,221.8z" />
                                                        <path d="M179,322.7v-83.7h93.9c4.7,0,8.5-3.8,8.5-8.5s-3.8-8.5-8.5-8.5H179c-9.4,0-17.1,7.6-17.1,17.1v83.7
				c2.4,34.9,14.8,68.4,35.8,96.4c2.8,3.8,8.1,4.7,11.9,1.9s4.7-8.1,1.9-11.9C192.7,383.9,181.5,354,179,322.7z" />
                                                        <path d="M313.1,283c-3.3-3.3-8.7-3.3-12.1,0l-63.3,63.3l-10-10c-3.3-3.2-8.7-3.2-12,0.1c-3.3,3.3-3.3,8.6-0.1,12l16,16
				c3.3,3.3,8.7,3.3,12.1,0l69.3-69.3C316.4,291.7,316.4,286.3,313.1,283z" />
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="service-text">
                                        <h4 class="title">Transparency & Security</h4>
                                        <p>All transactions are secure, and users can view their staking history and earnings transparently through the platform.</p>
                                    </div>
                                </div><!-- .service -->
                            </div><!-- .col -->
                        </div><!-- .row -->
                    </div>
                </div><!-- .container -->
            </section><!-- .section -->
            <section class="section section-testimonial bg-lighter" id="latest-posts">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-sm-9 col-md-7 col-10">
                <div class="section-head">
                    <h2 class="title">Read Our Latest Posts</h2>
                    <p class="lead">Stay updated with our latest posts about cryptocurrency!</p>
                </div>
            </div><!-- .col -->
        </div><!-- .row -->
        <div class="row gx-gs gy-5">
            <?php
            // Define the WordPress RSS feed URL
            $rss_url = "https://faucetminehub.com/read/feed/"; // Replace with your WordPress RSS feed URL

            // Use cURL to fetch the RSS feed
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rss_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $rss_data = curl_exec($ch);

            // Check if there was an error with cURL
            if (curl_errno($ch)) {
                echo "<p>Error fetching the RSS feed: " . curl_error($ch) . "</p>";
            }

            curl_close($ch);

            if ($rss_data) {
                // Load the RSS feed into SimpleXML
                $rss_feed = simplexml_load_string($rss_data);

                if ($rss_feed && isset($rss_feed->channel->item)) {
                    // Convert items to an array
                    $items = [];
                    foreach ($rss_feed->channel->item as $item) {
                        $items[] = $item;
                    }

                    // Limit to the latest 3 posts (you can increase this if needed)
                    $latest_posts = array_slice($items, 0, 3);

                    // Display each of the latest posts
                    foreach ($latest_posts as $post) {
                        // Truncate title to 50 characters max
                        $title = $post->title;
                        $truncated_title = strlen($title) > 50 ? substr($title, 0, 50) . '...' : $title;
                        
                        $link = $post->link;
                        
                        // Check for a non-empty description and truncate it
                        $description = !empty($post->description) ? strip_tags(html_entity_decode($post->description)) : 'No description available.';
                        $truncated_description = substr($description, 0, 100) . '...';
                        
                        // Attempt to extract the image from the content:encoded section
                        $content = $post->children('content', true)->encoded;
                        $image_url = '';

                        if ($content) {
                            // Use regex to extract the first image URL from content
                            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
                            $image_url = $image['src'] ?? '';
                        }
            ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="review">
                                <div class="review-content card card-shadow round-xl">
                                    <div class="card-inner card-inner-lg">
                                        <?php if ($image_url): ?>
                                            <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Post Image" class="post-image" style="width:100%; border-radius: 8px; margin-bottom: 15px;">
                                        <?php endif; ?>
                                        <h3 class="post-title"><a href="<?php echo htmlspecialchars($link); ?>" target="_blank" style="text-decoration: none; color: #333;"><?php echo htmlspecialchars($truncated_title); ?></a></h3>
                                        <div class="review-text">
                                            <p><?php echo htmlspecialchars($truncated_description); ?></p>
                                        </div>
                                        <a href="<?php echo htmlspecialchars($link); ?>" class="read-more" target="_blank" style="color: #007bff; font-weight: bold;">Read more</a>
                                    </div>
                                </div>
                            </div><!-- .review -->
                        </div><!-- .col -->
            <?php
                    }
                } else {
                    echo "<p>Could not parse the RSS feed or no items found in the feed.</p>";
                }
            } else {
                echo "<p>Could not fetch the RSS feed data. Please check the feed URL and server settings.</p>";
            }
            ?>
        </div><!-- .row -->
    </div><!-- .container -->
</section><!-- .section -->

            <section class="section section-facts bg-grad-a" id="facts">
                <div class="container">
                    <div class="row g-gs align-items-center">
                        <div class="col-lg-5">
                            <div class="text-block is-dark pe-xl-5">
                                <h2 class="title">Automatic Withdrawals</h2>
                                <p class="lead">At the end of the staking period, your principal and rewards are automatically available for withdrawal to your wallet or reinvestment.</p>
                            </div><!-- .text-block -->
                        </div><!-- .col -->
                        <div class="col-lg-7">
                            <div class="row text-center g-gs">
                                <div class="col-sm-4 col-6">
                                    <div class="card card-full round-xl">
                                        <div class="card-inner card-inner-md">
                                            <div class="h1 fw-bold text-purple" id="total-users"><?php echo htmlspecialchars($total_users); ?></div>
                                            <div class="h6 text-base">Users <br> Served</div>
                                        </div>
                                    </div>
                                </div><!-- .col -->
                                <div class="col-sm-4 col-6">
                                    <div class="card card-full round-xl">
                                        <div class="card-inner card-inner-md">
                                            <div class="h1 fw-bold text-success" id="total-deposits"><?php echo htmlspecialchars($total_deposits); ?></div>
                                            <div class="h6 text-base">Deposit <br> Secured</div>
                                        </div>
                                    </div>
                                </div><!-- .col -->
                                <div class="col-sm-4">
                                    <div class="card card-full round-xl">
                                        <div class="card-inner card-inner-md">
                                            <div class="h1 fw-bold text-pink" id="total-withdrawals"><?php echo htmlspecialchars($total_withdrawals); ?></div>
                                            <div class="h6 text-base">Withdraw <br> Processed</div>
                                        </div>
                                    </div>
                                </div><!-- .col -->
                            </div><!-- .row -->
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .container -->
                <!-- <div class="nk-ovm shape-b shape-cover shape-top"></div> -->
                <div class="nk-ovm shape-b shape-cover"></div>
            </section><!-- .section -->
            <footer class="footer bg-indigo is-dark bg-lighter" id="footer">
                <div class="container">
                    <div class="row g-3 align-items-center justify-content-md-between py-4 py-md-5">
                        <div class="col-md-3">
                            <div class="footer-logo">
                                <a href="/" class="logo-link">
                                    <img class="logo-light logo-img" src="./images/newlogo-white.png" srcset="./images/newlogo- white.png" alt="logo">
                                    <img class="logo-dark logo-img" src="./images/newlogo-white.png" srcset="./images/newlogo-white.png" alt="logo-dark">
                                </a>
                            </div><!-- .footer-logo -->
                        </div><!-- .col -->
                    </div>
                    <hr class="hr border-light mb-0 mt-n1">
                    <div class="row g-3 align-items-center justify-content-md-between py-4">
                        <div class="col-md-8">
                            <div class="text-base">Copyright &copy; 2024 FaucetMineHub.</div>
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .container -->
            </footer><!-- .footer -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script>
        // Function to fetch stats data from the server
        function fetchStats() {
            $.ajax({
                url: 'stats_api.php', // The backend script to get the stats
                method: 'GET',
                success: function(data) {
                    // Update the stats on the page
                    $('#total-users').text(data.total_users);
                    $('#total-withdrawals').text(data.total_withdrawals);
                    $('#total-deposits').text(data.total_deposits);
                }
            });
        }

        // Call fetchStats every 5 seconds for real-time updates
        setInterval(fetchStats, 5000);

        // Initial fetch when the page loads
        $(document).ready(function() {
            fetchStats();
        });
    </script>
    <script src="home/js/bundle.js"></script>
    <script src="home/js/scripts.js"></script>
</body>

</html>
