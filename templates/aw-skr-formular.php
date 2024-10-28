<?php
/**
 * Provide formular with shortcode [aw_skr]
 *
 * This file is used to markup the public-facing aspects of the plugin. Copy to your Theme-DIR to modify.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/templates
 * @author     Active Websight <info@active-websight.de>
 */

?>

<div id="skr" class="<?php echo $style; ?>">

	<div class="skr-form-wrap">

		<form class="skr-form floating-labels" id="skr-form" method="POST" action="edit" novalidate>
			<fieldset>
				<div class="error-message"></div>
				<legend><?php echo esc_html( $title ); ?></legend>

				<div class="skr-input-wrap">

					<div class="skr-icon" data-validation="required|number" data-validation-type="input" data-ux="emptyOnZero">
						<label class="skr-label with-span" for="skr-einkommen-ehemann">Einkommen Ehemann <span>€ netto/mtl.</span></label>
						<input class="budget" type="text" name="skr-einkommen-ehemann" id="skr-einkommen-ehemann" value="<?php echo esc_html( str_replace('.', ',', $data['skr-einkommen-ehefrau'] ) ); ?>" required>
					</div>

					<div class="skr-icon" data-validation="required|number" data-validation-type="input" data-ux="emptyOnZero">
						<label class="skr-label with-span" for="skr-einkommen-ehefrau">Einkommen Ehefrau <span>€ netto/mtl.</span></label>
						<input class="budget" type="text" name="skr-einkommen-ehefrau" id="skr-einkommen-ehefrau" value="<?php echo esc_html( str_replace('.', ',', $data['skr-einkommen-ehefrau'] ) ); ?>" required>
					</div>

					<div class="">
						<h4>Anzahl der unterhaltsberechtigten Kinder</h4>
						<p class="skr-select skr-icon">
							<select name="skr-kinder" class="user">
								<?php
								for ( $i = 0; $i <= 10; $i++ ) :
									$label = 0 === $i ? '0 -- Keine' : $i;
								?>
								<option value="<?php echo $i; ?>"><?php echo $label; ?></option>
								<?php endfor; ?>
							</select>
						</p>
					</div>

					<div class="">
						<h4>Anzahl der Rentenversicherungen beider Ehegatten</h4>
						<p class="skr-select skr-icon">
							<select name="skr-rversicherungen" class="company">
								<?php
								for ( $i = 0; $i <= 10; $i++ ) :
									$label = 0 === $i ? '0 -- Keine' : $i;
								?>
								<option value="<?php echo $i; ?>"><?php echo $label; ?></option>
								<?php endfor; ?>
							</select>
						</p>
					</div>

					<div class="">
						<h4>Weitere Angaben</h4>
						<ul class="skr-form-list">
							<li>
								<input type="hidden" name="skr-unter3" id="skr-unter3" value="0" />
								<input type="checkbox" name="skr-unter3" id="skr-unter3-ja" value="1" <?php echo esc_html( 1 == intval( $data['skr-unter3'] ) ? ' checked' : '' ); ?>>
								<label for="skr-unter3-ja">Ehe unter 3 Jahren</label>
							</li>
							<li>
								<input type="hidden" name="skr-ehevertrag" id="skr-ehevertrag" value="0" />
								<input type="checkbox" name="skr-ehevertrag" id="skr-ehevertrag-ja" value="1" <?php echo esc_html( 1 == intval( $data['skr-ehevertrag'] ) ? ' checked' : '' ); ?>>
								<label for="skr-ehevertrag-ja">Versorgungsausgleich durch einen Ehevertrag ausgeschlossen?</label>
							</li>
						</ul>
					</div>

					<?php if ( ! empty( $dsgvo ) ) : ?>

					<div class="required skr-dsgvo" data-validation="required" data-validation-type="checkbox">
						<h4>Datenschutz</h4>
						<ul class="skr-form-list">
							<li>
								<input type="checkbox" name="skr-datenschutz" id="skr-datenschutz" value="1">
								<label for="skr-datenschutz"><?php echo $dsgvo; ?></label>
							</li>
						</ul>
					</div>

					<?php endif; ?>

					<div class="skr-action">
						<?php echo $grecaptcha; ?>
						<button type="submit">Scheidungskosten berechnen</button>
					</div>
				</div>
			</fieldset>

		</form>
	</div>

	<div class="skr-result-wrap">

		<div class="skr-result">
			<h4>Ihr Ergebnis</h4>
			<div class="skr-action top">
				<a href="#" class="button edit js--AW_SKR-edit">Angaben ändern</a>
			</div>

			<div class="row row-0">
				<div class="head">Gegenstandswert Ehesache <a href="#" class="icon-info" data-info="skr-info-gemeinsames-einkommen">?</a></div>
				<div class="value" data-skr-update="gw-ehe-ergebnis"></div>
			</div>
				<div class="row info-popup" id="skr-info-gemeinsames-einkommen">
					<div class="subrow">
						<div class="head">Gemeinsames Einkommen:</div>
						<div class="value" data-skr-update="gw-ehe-gemeinsames-einkommen"></div>
					</div>
					<div class="subrow">
						<div class="head">Abzug Kinder <span>(250 € pro Kind)</span></div>
						<div class="value val-minus" data-skr-update="gw-ehe-kinder"></div>
					</div>
					<div class="subrow double">
						<div class="head">wird verdreifacht</div>
						<div class="value"><strong>3x <b data-skr-update="gw-ehe-ergebnis-monat"></b></strong></div>
					</div>
					<div class="js--AW_SKR-ehe-mv-notice">
						<div class="subrow">
							<div class="head"><strong>Hinweis:</strong> Der gesetzliche Mindestwert für eine Scheidung beträgt:</div>
							<div class="value"><strong>3.000,00 €</strong></div>
						</div>
					</div>
					<div class="subrow close">
						<a href="#" class="button edit js--AW_SKR-infoclose">Schließen</a>
					</div>
				</div>
			<div class="row row-1">
				<div class="head">Gegenstandswert Versorgungsausgleich * <a href="#" class="icon-info" data-info="skr-info-versorgung">?</a></div>
				<div class="value val-plus" data-skr-update="gw-versorgung"></div>
			</div>
				<div class="row info-popup" id="skr-info-versorgung">
					<div class="js--AW_SKR-versorgung-rversicherungen">
						<div class="subrow">
							<div class="head">je Rentenversicherung<br>10% vom Gegenstandswert Ehesache:</div>
							<div class="value"><strong><b data-skr-update="gw-versorgung-rvs"></b>x <b data-skr-update="gw-versorgung-10"></b></strong>
								<div class="js--AW_SKR-versorgung-mv-notice">jedoch mindestens 1.000 €</div>
							</div>
						</div>
					</div>
					<div class="subrow close">
						<a href="#" class="button edit js--AW_SKR-infoclose">Schließen</a>
					</div>
				</div>
			<div class="row row-sum row-sum-line">
				<div class="head">Gegenstandswert gesamt</div>
				<div class="value" data-skr-update="gw-ehe"></div>
			</div>

			<div class="row-dist"></div>

			<div class="row row-0 row-res">
				<div class="head">Anwaltskosten</div>
				<div class="value" data-skr-update="anwaltskosten"></div>
			</div>
			<div class="row row-1 row-res">
				<div class="head">Gerichtskosten **</div>
				<div class="value val-plus" data-skr-update="gerichtskosten"></div>
			</div>
			<div class="row row-final row-sum-line">
				<div class="head">Scheidungs&shy;kosten ***</div>
				<div class="value" data-skr-update="scheidungskosten"></div>
			</div>

		</div>

		<div class="skr-action">
			<a href="#" class="button edit js--AW_SKR-edit">Angaben ändern</a>
			<span data-skr-update="kontaktbutton"></span>
		</div>

		<div class="skr-info">
			<p>* Bei einer Ehe unter drei Jahren wird davon ausgegangen, dass die Ehegatten nicht die Durchführung des Versorgungsausgleichs wünschen; bei Ausschluss des Versorgungsausgleichs durch Notarvertrag wird der gesetzliche Mindestwert von 1.000 € in Ansatz gebracht; ebenso gilt dieser gesetzliche Mindestwert von 1.000 € bei einer Ehe über drei Jahren, wenn der Versorgungsausgleich durch notarielle Urkunde ausgeschlossen ist.</p>
			<p>** Die Gerichtskosten sind von beiden Ehegatten hälftig zu tragen. Der Antragsteller muss diese Gerichtskosten zunächst verauslagen und erhält am Ende des Verfahrens die anteiligen Gerichtskosten erstattet.</p>
			<p>*** Ausgegeben werden die Rechtsanwaltsgebühren für einen Rechtsanwalt und die Gerichtskosten für beide Eheleute. Für den Fall, dass beide Eheleute jeweils einen Anwalt beauftragen, fallen die Anwaltskosten zwei Mal an. Für die Durchführung des Scheidungsverfahrens ist grundsätzlich bei einer einvernehmlichen Scheidung nur die Beauftragung eines Rechtsanwaltes durch einen Ehegatten erforderlich.</p>
		</div>

	</div>

	<?php echo $poweredby; ?>

</div>

<?php
