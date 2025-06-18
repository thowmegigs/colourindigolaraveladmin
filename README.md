1- vendor order mein status column mein success means order was succesfully placed bu customer 
  failed  means either payment cancelled after creating order 
2- each vendor order has its status updates in vendor_order_status_updates table 
3- Each vendor order table has is_proved_by_vendor column so that if approved the new order from vendor then only admin can transfer it to shiprocket or directly it will be transfered to shiprocket 
4- in shiprocket each registered vendor on laraevel plateform aslo has to automatically gets registred their location with uniwue name on shiprocket so that delivery can be succesfuly when trandfering order 
5-for  each vendor has to create spreate shioment orders caleed vendor orders instead of whole order combined 
6- when setting up smtp mail server on ubuntu vps 
   
   u add txt records for
   MX record === type=MX name=@ value="mail.colourindigo.co" priority=10  
   spf ""    ===type=TXT name="mail" value="some string give by mail server"
   _dmrc ""=== type=TXT name="_dmarc" value="provided by mail server"
  7-we use postfxi as smtp server like nginx for webserver and devecot for string username and password to login webmail using roundcube 
    to create user and password to login in roundcube web interface 
    use username =support@colourindigo and password ="set by u "
    to create the users 
    -1 create a databse named "mailserver" and create one table in that virtual_users with username and password column
    2-doveadm pw -s SHA512-CRYPT
       copy and paste the generated hash password in the mysql table with support@colourindigi.com username 
    3- also paste the password in here sudo nano /etc/dovecot/users,add this 
        support@colourindigo.com:{SHA512-CRYPT}$6$yourhashedpass::::::
      
    4-edit sudo nano /etc/dovecot/dovecot-sql.conf.ext,set according to this 
        driver = mysql
        connect = host=localhost dbname=mailserver user=support@colourdingioc.com password=your_db_pass

        default_pass_scheme = SHA512-CRYPT

        password_query = SELECT username as user, password FROM virtual_users WHERE username = '%u'

        user_query = SELECT '/home/mail/%u/Maildir' as home, 'vmail' as uid, 'vmail' as gid
      6- install devcot-mysql package otherwise mysql driver not found error when restarting devot status checkign 
      5-edit /etc/dovecot/conf.d/10-auth.conf, uncommetn as per follwing 
        !i  nclude auth-sql.conf.ext
      6-edit  /etc/dovecot/conf.d/10-mail.conf:
          mail_location = maildir:/home/mail/%u
        7- check if directry exist /home/mail/support@colourindigo.
           sudo mkdir -p /home/mail/support@colourindigo.com/Maildir
         sudo chown -R vmail:vmail /home/mail
        8-test the user login using cmd 
        doveadm auth test support@colourindigo.com 'your-password-here'
        9-sudo systemctl restart dovecot
        sudo systemctl restart postfix
        sudo systemctl reload nginx
====================
1-mail worker in pm2 is running for bullmq jobs 