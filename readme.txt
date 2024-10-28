=== Scheidungskostenrechner ===
Contributors: activewebsight
Tags: scheidung, online, rechner, berechnen, rechtsanwalt, anwalt, vergütungsgesetz
Requires at least: 4.4
Tested up to: 5.6
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ein Scheidungskostenrechner Plugin für Ihre Homepage.

== Description ==

Ein Scheidungskostenrechner Plugin für Ihre Homepage. Berechnen Sie die Scheidungskosten und lassen Sie sich von Interessenten eine E-Mail zusenden.

Bereitgestellt von RA Christian Kieppe, Online-Scheidung-Deutschland.de

Um das Plugin nutzen zu können, gehen Sie in den Einstellungen des Plugins 'AW SKR', akzeptieren Sie dort die AGBs und modifizieren Sie die gewünschten Einstellungen.

Wenn Sie den Scheidungskostenrechner als Widget einfügen, können Sie in den Widget-Einstellungen den Titel und die Breite des Widgets ändern.
Alternativ können Sie auch den Shortcode `[aw_skr]` an der gewünschten Stelle in einer/m Seite/Beitrag oder im Template verwenden.

Die Berechnung erfolgt nach dem Rechtsanwaltsvergütungsgesetz und der entsprechenden, aktuellen Gebührentabelle.

Die PRO-Version des Scheidungskostenrechners finden Sie unter [Scheidungskostenrechner.org](http://scheidungskostenrechner.org).


### 3rd Party Resources
* [CMB2](https://cmb2.io)
* [WPBP/template](https://github.com/WPBP/template)

== Installation ==

1. Den Ordner `aw-skr` in `/wp-content/plugins/` hochladen.
2. Aktivieren Sie das Plugin im 'Plugins' Menü von WordPress.
3. In den Einstellungen des Plugins 'AW SKR' die AGBs akzeptieren und das erste Setup machen.

== Changelog ==

= 1.0.6 =
* Getestet mit WP 5.6
* ADD: Neue Berechnungstabelle (automatisch gültig ab 2021)
* FIX: JS - Codeoptimierung (Console-Fehler bei ScrollTo-Links)

= 1.0.5 =
* Getestet mit WP 5.3.2 und PHP 7.4.1
* FIX: Update CMB2 -> 2.6.0
* FIX: Update WPBP/template -> 1.0.1
* FIX: Typo auf der Plugin-Einstellungsseite

= 1.0.4 =
* Eingabemakse optimiert - Radio-Buttons durch Checkboxen ausgetauscht, Standardwerte für Einkommen
* FIX: JS - bei Klick in ein Eingabefeld mit Wert 0 wird der Wert entfernt und es kann sofort losgetippt werden
* FIX: CSS - Eingabefelder verlieren bei manchen Templates die Symbole

= 1.0.3 =
* FIX: Problem mit CMB2 - Einstellungsseite wird nicht dargestellt

= 1.0.2 =
* Kleinere Text-Korrekturen
* ADD: Checkbox zum DSGVO-Text
* FIX: Update CMB2 (PHP 7.2.x Fehler)
* FIX: JS/CSS Kompatibilität

= 1.0.1 =
* Kleinere Text-Korrekturen
* Problem mit CMB2 - PHP Version muss < 7.2.x sein - wird gefixt, sobald CMB2 ein Update bereitstellt
* JS/CSS Fixes für optimierte schmale Darstellung

= 1.0.0 =
* Erste Version.
