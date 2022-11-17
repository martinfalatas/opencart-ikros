Ikros Tool (V1.2) pre OpenCart 2.x
===============================================

Ikros Tool rozšírenie umožní prepojenie Vášho opencart e-shopu s 
online fakturačnou aplikáciou Ikros.



Požiadavky a Obmedzenia
=======================

Tento balíček bol testovaný a je určený pre verzie OC 2.0.0.0 až 2.3.0.2 
Nepoužívajte rozšírenie s inou verziou Opencart.

Rozšírenie predpokladá aplikovanie maximálne jednej dane na jeden produkt.

Modul môže upravovať alebo pridávať produkty do Vášho e-shopu.
Pred samotnou inštaláciou modulu a jeho prvým použitím odporúčam zálohovať 
zdrojové súbory a databázu Vášho eshopu!



Inštalácia
==========

V OpenCart admin backende postupujte podľa nasledujúcich krokov:

Krok 1)
  Vstúpte do System > Tool > Backup/Restore a urobte zálohu Vášho e-shopu.

Krok 2)
	Vstupte do Extensions > Extension Installer

Krok 3)
	Uploadnite ikros-2.x.ocmod.zip

Krok 4)
	Vstúpte do Extensions > Modifications
	Mali by ste tam násjť modifikáciu 'Ikros'

Krok 5)
	Kliknite na tlačidlo refresh v pravej hornej časti okna

Krok 6)
	Vstúpte do System > Users > User Group > Edit Administrator

Krok 7)
	Nastavte access and modify permissions pre 'tool/ikros'

Krok 8)
  Vstúpte do System > Tools > Ikros
  Aktivujte tlačidlo ŠTART (vytvorenie dvoch pomocných tabuliek v databáze)
  
Krok 9)
  Vytvorte kategóriu s názvom Kros

To je všetko! Rozšírenie máte nainštalované. Na karte Nastavenia vložte 
Autorizačný kľúč ktorý získate v aplikácii Ikros a vyplnte si údaje, ktoré 
chcete prenášať do aplikácie Ikros. Pre detailnejšie popísanie jednotlivých 
polí nastavení navštívte prosím <http://openquiz.eu>

Ak počas inštalácie narazíte na chybovú hlášku "Could not connect as ......" 
počas uploadovania rozšírenia prostredníctvom  Extension Installer, 
pravdepodobne máte vypnutú FTP podporu na vašom hostingu. 
V tom prípade môžete skúsiť nasledujúcu Opencart opravu:

https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=18892
https://www.youtube.com/watch?v=cBMHfnbSVVg



CRON:
=====

Pre automatizáciu importov a exportov môžete využiť nasledujúce cesty:
http://vasa-domena/index.php?route=tool/ikros/import_products&password=vase-heslo -> import produktov
http://vasa-domena/index.php?route=tool/ikros/export_orders&password=vase-heslo -> export objednávok
http://vasa-domena/index.php?route=tool/ikros/export_invoices&password=vase-heslo -> export faktúr

V prípade ak máte obmedzený počet cron úloh, môžete využiť zgrupené volania:
http://vasa-domena/index.php?route=tool/ikros/export_documents&password=vase-heslo -> export faktúr a objednávok
alebo
http://vasa-domena/index.php?route=tool/ikros/import_export&password=vase-heslo -> import produktov + export objednávok + export faktúr




Následná podpora a modifikácie rozšírenia
=========================================

Tento nástroj bol úspešne testovaný na štandardnej OpenCart inštalácii verzie 
2.0.0.0 až 2.3.0.2
Nepoužívajte iné verzie opencartu s týmto rozšírením.

