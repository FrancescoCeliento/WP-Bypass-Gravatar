# WP-Bypass-Gravatar
Plugin per disabilitare totalmente Gravatar da Wordpress sostituendo gli avatar con un set personalizzato. Il plugin rimuove al 100% le chiamate verso i servizi di Gravatar.

![WP Bypass Gravatar](./WP%20Bypass%20Gravatar.png)

Gli avatar visualizzati sono calcolati in base all'indirizzo email collegato al commento o all'account. Un avatar può essere associato a più utenti, ma ogni utente avrà sempre lo stesso avatar.

Con questo plugin blocchiamo completamente le chiamate verso Gravatar, gestendo localmente la visualizzazione degli Avatar.

## Funzionalità 
🔒 Gravatar completamente bypassato

⚡ Cache via transient (performance)

🧩 Fallback configurabile

🔢 Set avatar espandibile

⚙️ Pannello admin semplice e pulito

🛡 Privacy & GDPR OK

## Personalizzare pacchetto avatar
Fonte avatar di default: https://www.figma.com/community/file/996122665902209471/user-avatars-customizable-vector-profile-pictures

Per sostituire o aggiungere gli avatar con un set personalizzato è necessario aggiungere o sostituire i file nella cartella ''avatar''. I file devono essere numerati da 1 a x e devono essere in formato PNG. La numerazione deve essere effettuata anteponendo gli zeri in modo che i nomi dei file risultino formattati come segue: 001.png, 002.png, ..., 100.png oppure 0001.png, 0002.png, ..., 2500.png. Successivamente nel pannello Impostazioni > WP Bypass Gravatar indicare il nuovo range di Avatar.

## Installazione
È un plugin alpha, non presente nel repo ufficiale di Wordpress. Per installare [scaricare questo repository](https://github.com/FrancescoCeliento/WP-Bypass-Gravatar/archive/refs/heads/main.zip), decomprimere l'archivio, e caricare la cartella risultante via ftp nella cartella plugins di Wordpress. Successivamente abilitarla nel pannello plugin di Wordpress.

## Caffè
Nel caso ti facesse piacere [offrirmi un caffè](https://www.paypal.com/paypalme/francescoceliento).
