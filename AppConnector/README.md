# STROMDAO-Corrently App Connector
Ermöglicht eine Übernahme von Schaltvorgängen direkt in die Corrently App zur Kompensation und Erreichen der Klimaziele.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang
- Verknüpfbar mit einem Switch in Symcon
- Verknüpfbar mit Stromzählern in Watstunden
- Trigger des Corrently Webservices zur Übermittlung des Verbrauchsevents
- Protokollierung der Ergebnisse unter einer eindeutigen Kompensations Kennung (Account)

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.0

### 3. Software-Installation

* Über den Module Store das 'CO2 Emission Strom'-Modul installieren.
* Alternativ über das Module Control folgende URL hinzufügen: https://github.com/energychain/ips_co2zaehler

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' ist das 'CO2 Emission Strom'-Modul unter dem Hersteller 'STROMDAO GmbH' aufgeführt.

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Postleitzahl | Postleitzahl in Deutschland, auf die eine spezifische CO2 Emission räumlich und zeitlich verortet werden soll
meteringvariable | Integer Variable, die einen Stromzähler in Wattstunden (wh) enthält.

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
co2g_standard   | integer | Emitierte CO2 Menge im Betrachtungszeitraum in Gramm für einen konventionellen Stromtarif
co2g_oekostrom   | integer | Emitierte CO2 Menge im Betrachtungszeitraum in Gramm für einen Ökostromtarif (zertifiziert)
account | string | Stromdao/Corrently Kennung zur Kompensation dieses Zählers

#### Profile

Name   | Typ
------ | -------
co2gramm | Integer

### 6. WebFront

N/A

### 7. PHP-Befehlsreferenze

`void CO2_setReading(integer $InstanzID);`
Aktualisiert die Kohlendioxid Menge, wird automatisch durch einen Trigger aufgerufen.

Beispiel:
`CO2_setReading(12345);`
