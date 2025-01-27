![screencapture-faucetminehub-2025-01-27-13_46_20](https://github.com/user-attachments/assets/c551feea-1d01-4149-b70c-e0408bf32ef7)
![screencapture-faucetminehub-dashboard-2025-01-27-13_44_55](https://github.com/user-attachments/assets/67201874-fd21-4235-8aa3-5cb252fa97ed)
![screencapture-faucetminehub-staking-2025-01-27-13_45_14](https://github.com/user-attachments/assets/bfa80920-3608-428f-8d5d-b85dfa981e50)


# Website Setup Instructions

There is a lot of stuff that needs to be changed due to the website likely being built by a newbie.  
I have gathered the files that need to be updated.

---

## REQUIREMENTS:

**PHP 8.2**  
Extensions: (MUST BE ACTIVE)  
- `INTL`  
- `bcmath`  
- `GD`  
- `ioncube_loader`

---

## Files to Update:

1. **`config/config.php`**  
2. **`deposit.php`**  
   - Just make changes carefully to ensure the code remains functional. Update the following:  
     ```html
     <input type="hidden" name="merchant_username" value="wepayou"> <!-- Your FaucetPay username, must have Merchant API enabled https://faucetpay.io/merchant -->
     <input type="hidden" name="callback_url" value="https://faucetminehub.com/payment_callback"> <!-- Only change the domain -->
     <input type="hidden" name="success_url" value="https://faucetminehub.com/success"> <!-- Only change the domain -->
     <input type="hidden" name="cancel_url" value="https://faucetminehub.com/cancel"> <!-- Only change the domain -->
     ```
3. **`.htaccess`**  
   - Update the domain name.  
4. **`sitemap.xml`**  
   - Use your own sitemap.  
5. **`robots.txt`**  
   - Update accordingly.  
6. **`cron_update_staking.php`**  
   - Change the `secter_key` on **line 6**.  
7. **hCAPTCHA & reCAPTCHA**  
   - Update the following files:  
     - **`daily.php`**: Lines 107 & 453  
     - **`register.php`**: Lines 44 & 233  
     - **`request_password_reset.php`**: Lines 9, 83, 105 & 108  

---

## Database Import:  
- Import the database file located in the root directory: `database.sql`.

---

##Cron Jobs: (THIS IS A MUST FOR UPDATING STAKING AND COIN PRICE)

1. **CoinGecko Price Updates (Every 5 minutes):**
```https://YOURDOMAIN.com/coingecko.php```

2. **Cron Expression Updates (Every 5 minutes):**
```https://YOURDOMAIN.com/cronexp```

3. **Staking Updates (Every 1 minute):**
```https://YOURDOMAIN.com/cron_update_staking.php?key=####```
- Replace #### with your key (found in cron_update_staking.php on line 6).
  
---

## Support:
- If you have no idea how to set this up, feel free to contact me on Telegram: @foursedev.
- I’ll gladly set it up for you for $10 (HEHE).

---

## Donate:
- FaucetPay Username: ```ubimaru```
- Accepting direct transfers ❤️
