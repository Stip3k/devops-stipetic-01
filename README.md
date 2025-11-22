Vagrant in Cloud-init deplyment Laravel aplikacije

## Splošno o delovanju
In Vagrant in cloud-init deployment imata v principu isti postopek postavitve spletne  aplikacije. Tukaj so opisani po korakih:
1. Posodbitev in namestitev paketov
2. Nastavitev MySQL uproabnika in njegovih privilegijev
3. Uvoz arhiva baze spletne aplikacije
4. Namestitev Composerja in namestitev datotek spletne aplikacije v /var/www/ucilnice
5. Namestitev certifikatov
6. Nastavitev Nginx in zapis nastavitev za spletno aplikacijo v /etc/nginx/sites-availabe
Guest OS ima odprta vrat 443 in 80. 443 je preusmerjen na gostitelja na vrata 8443, vrata 80 iz guest OS-a pa na vrata gostitelja 8000. Preusmeritev iz 80 na vrata 443 dela težave, ker mora preusmeriti iz vrata 8000 na 8443, zato je priporočeno samo iti na `https://<IP-gostiteljske-naprave>:8443`.
Certifikati so že prej bili ustvarjeni in so priloženi. Sama spletna aplikacija uproablja ogrodje Laravel v jeziku PHP z dodatnimi moduli.
## Koraki deplyment-a z Vagrant
Namestitev Vagrant:
```bash
wget -O - https://apt.releases.hashicorp.com/gpg | sudo gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(grep -oP '(?<=UBUNTU_CODENAME=).*' /etc/os-release || lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update && sudo apt install vagrant virtualbox
```
Prenesite datoteko laravel-app-vagrant ter se postavite na koren datoteke. Od tam rabite pognati ukaz:
`vagrant up`
Po končanem nastavljanju je spletišče dosegljivo na `https://<IP-gostiteljske-naprave>:8443`.
## Koraki deployment-a z Cloud-init
Namestitev Incus:
```bash
curl -fsSL https://pkgs.zabbly.com/key.asc | gpg --show-keys --fingerprint

sh -c 'cat <<EOF > /etc/apt/sources.list.d/zabbly-incus-lts-6.0.sources
Enabled: yes
Types: deb
URIs: https://pkgs.zabbly.com/incus/lts-6.0
Suites: $(. /etc/os-release && echo ${VERSION_CODENAME})
Components: main
Architectures: $(dpkg --print-architecture)
Signed-By: /etc/apt/keyrings/zabbly.asc

EOF'
```

Prenesite datoteko laravel-app-cloud-init ter se postavite na koren datoteke. Od tam rabite pognati ukaz:
```bash
incus launch images:ubuntu/22.04/cloud laravel-app   --config=user.user-data="$(cat cloud-config.yml)"   -c limits.cpu=2   -c limits.memory=2GB

incus file push -r ucilnice/ laravel-app/opt/
incus file push -r ssl/ laravel-app/opt/
incus file push db.sql laravel-app/opt/

incus exec laravel-app -- /tmp/setup-laravel.sh

incus config device add laravel-app https proxy   listen=tcp:0.0.0.0:8443   connect=tcp:127.0.0.1:443
incus config device add laravel-app http proxy   listen=tcp:0.0.0.0:8000   connect=tcp:127.0.0.1:8
```
Po končanem nastavljanju je spletišče dosegljivo na `https://<IP-gostiteljske-naprave>:8443`.