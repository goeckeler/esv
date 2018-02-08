/*
Copyright (C) 2008 Harald Herberth

Dokumentation
http://handball-sr-mittelfranken.de/tabellen/

Beispiele
<!-- Einbinden der CSS und JS Dateien -->
<link rel="stylesheet" type="text/css" href="http://handball-sr-mittelfranken.de/tabellen/tabellen.css">
<script src="http://handball-sr-mittelfranken.de/tabellen/jquery.js" type="text/javascript"></script>
<script src="http://handball-sr-mittelfranken.de/tabellen/tabellen.js" type="text/javascript"></script>

<!-- Erzeugt einen Eintrag mit Links auf alle Tabellen, die in die Seite eingebaut wurden -->
<div class="srsLinks"></div>

<!-- Tabelle mit voller Anzeige -->
<div class="srsTab" srsKlasse="BOLWJA" srsVerein="Zirndorf"></div>
<div class="srsTab" srsKlasse="BOLMJA" srsVerein="Altenberg"></div>
<div class="srsTab" srsKlasse="BKM2" srsVerein="Altenberg"></div>

<!-- Erzeugt obige Eintr�ge automatisch f�r alle Tabellen eines Vereins -->
<div class="srsTabVerein" srsTitle="lang" srsVerein="Zirndorf" srsAuchSpiele=1></div>

<!-- Spielplan f�r eineKlasse -->
<div class="srsPlan" srsKlasse="BOLWJA" srsVerein="Zirndorf" srsAlle=1></div>

<!-- Gesamt-Spielplan f�r einen Verein, in ganz Bayern -->
<div class="srsPlanVerein" srsVerein="Zirndorf" srsAlle=1></div>

*/
jQuery(document).ready(function($){
	if (window.srsLoaded) return;
	window.srsLoaded = true;
	$.ajaxSettings.cache = false;
	var links = $("div.srsLinks").empty();
	var anchors = [];
	// Eine einzelne Tabelle aus den Daten erstellen
	var create_table = function(header, fields, data) {
		var h = '<table>';
		if (header !== '-') {
			h += '<tr>';
			$.each(header.split(/;/), function(i,val) {
				h += "<th>" + val + "</th>";
			});
			h += '</tr>';
		}
		$.each(data.constructor == Array ? data : [data], function(i, val) {
			h += "<tr>";
			$.each(fields.split(/;/), function(j, f) {
				var i, fv, ff, fe, fev, pat, allnull;
				// wird eine Zelle aus mehreren Werten zusamengebaut? (expr+expr+expr...)
				fv = ""; allnull = true;
				fe = f.split(/\+/);
				for (i = 0; i < fe.length; i++) {
					// ein Ausdruck
					// feld started mit %, also Konstante
					if (fe[i].match(/^%/))
						fev = fe[i].substr(1);
					else {
						ff = fe[i].split(/\/|!|\xa7/); // xa7 = �
						fev = (val[ff[0]] && val[ff[0]].$ ? val[ff[0]].$ : '');
						if (fev.length > 0) allnull = false;
						// is there a pattern match and replace in the field expression (field/pat/replace)
						ff = fe[i].split(/\//);
						if (ff.length == 3) {
							pat = new RegExp(ff[1]);
							fev = fev.replace(pat,ff[2]); 
						}
						// is there a substring expression (field�start�len or sep by !)
						ff = fe[i].split(/\xa7/);
						if (ff.length == 3) {
							fev = fev.substr(ff[1],ff[2]); 
						}
						ff = fe[i].split(/!/);
						if (ff.length == 3) {
							fev = fev.substr(ff[1],ff[2]); 
						}
					}
					fv = fv + fev;
				}
				if (allnull) fv = "";
				h += "<td>" + (fv == '' ? '&nbsp;' : fv) + "</td>";
			});
			h += "</tr>";
		});
		h += '</table>';
		return h;
	};
	// eine tabelle in ein div mit formatieren
	var show_table = function(div, data, p) {
		$(div).empty();
		if (!data) return;
		if (!p.classtable) p.classtable = "srsTable";
		var h = create_table(p.header, p.fields, data);
		$(div).html(h);
		// class f�r tabelle und felder setzen
		$(div).find("table").addClass(p.classtable);
		var t = $(div).find("tr");
		$.each(p.classCol.split(/;/), function (i, val) {
			t.find("td:eq("+i+")").addClass(val).end()
			 .find("th:eq("+i+")").addClass(val).end();
		});
		// eigenen Verein einf�rben
		if (p.verein) t.filter(":contains('"+p.verein+"')").addClass("srsHome");
	};
	// alle tabellen (spiele und tabelle) fuer eine klasse
	var show_tables = function(div, data, p) {
		if (data && data.error && data.error.$) {$(div).append(data.error.$); return;}
		$(div).find(".srsLaden").remove();
		if (+p.minitab) p.auchspiele = "";
		var d; // temporary div to hold one table
		var aa;
		// show vorherige_Spiele
		if (p.auchspiele > 0 && data.Aktueller_Spielplan && data.Aktueller_Spielplan.vorherige_Spiele) {
			p.header = "Datum;Zeit;Heim;Gast;Tore";
			p.fields = "SpieldatumTag/^..(..).(..).(..)$/$3.$2.$1;Spieldatum!11!5;HeimTeam_Name_kurz;GastTeam_Name_kurz;Heim+%:+Gast";
			p.classCol = "l;l;l;l;c;c";
			d = $(document.createElement("div"));
			aa = data.Aktueller_Spielplan.vorherige_Spiele;
			//if (aa.constructor == Array) aa.reverse(); // ISS3 liefert Spiele davor in umgekehrter Reihenfolge
			show_table(d[0], aa, p);
			$(div).append(d);
		}
		// show Tabelle
		if (data.Tabelle || data.Aktueller_Spielplan && data.Aktueller_Spielplan.Tabelle) {
			d = $(document.createElement("div"));
			p.header = p.tabellenkopf || "Platz;Mannschaft;Spiele;g;u;v;Tore;Diff;Punkte";
			p.fields = p.tabellenspalten || "Platz;Team_Kurzname;Spiele;SpieleGewonnen;SpieleUnentschieden;SpieleVerloren;PlusTore+%:+MinusTore;DiffTore;PlusPunkte+%:+MinusPunkte";
			p.classCol = p.tabellenformat || "r;l;c;c;c;c;c;c;c;c";
			if (+p.minitab) {
				p.header = "Platz;Mannschaft;Punkte";
				p.fields = "Platz;Team_Kurzname;PlusPunkte";
				p.classCol = "r;l;c";
			}
			show_table(d[0], (data.Tabelle || data.Aktueller_Spielplan).Tabelle, p);
			// delete unwanted rows
			// welche Spalte hat denn den Platz, nicht das wir zuviel l�schen
			var sp = $.inArray("Platz", p.fields.split(/;/));
			if (+p.keineak > 0) {
				$(d).find("tr").find("td:eq("+sp+"):contains('254')").parent().remove();
			}
			if (+p.keineex > 0) {
				$(d).find("tr").find("td:eq("+sp+"):contains('255')").parent().remove();
			}
			// sort, since ISS3 does not always return the table sorted
			if (0) {
			var st = $(d).find("tr:gt(0)").get(); // first row contains header, do not sort
			$.each(st, function(i, val) {
				val.tabellen_rang = parseInt($(val).find("td:eq("+sp+")").text());
			});
			st.sort(function(a, b) {
				if (a.tabellen_rang < b.tabellen_rang) return -1;
				if (a.tabellen_rang > b.tabellen_rang) return 1;
				return 0;
			});
			$.each(st, function(i, val) {
				val.tabellen_rang = null;
				$(d).find("table").append(val);
			});
			}
			$(div).append(d);
		} else if (data.Teams) {
			p.header = "Team";
			p.fields = "sShortName";
			p.classCol = "l";
			d = $(document.createElement("div"));
			show_table(d[0], data.Teams.Teams, p);
			$(div).append(d);
		} else {
			$(div).html("Tabelle nicht gefunden");
		}
		// show kommende_Spiele
		if (p.auchspiele > 0 && data.Aktueller_Spielplan && data.Aktueller_Spielplan.kommende_Spiele) {
			p.header = "Datum;Zeit;Heim;Gast;Tore";
			p.fields = "SpieldatumTag/^..(..).(..).(..)$/$3.$2.$1;Spieldatum!11!5;HeimTeam_Name_kurz;GastTeam_Name_kurz;Heim+%:+Gast";
			p.classCol = "l;l;l;l;c;c";
			d = $(document.createElement("div"));
			show_table(d[0], data.Aktueller_Spielplan.kommende_Spiele, p);
			$(div).append(d);
		}
	};
	// alle ligen suchen, und anzeigen
	var lookup_tables = function() {
		var divs = $("div.srsTab");
		divs.each(function(i) {
			var o = $(this);
			var div = this;
			var p = {};
			var m;
			// get all attributes srs*
			$.each([
				"srsTitle","srsVerein","srsGruppe","srsKlasse","srsMinitab","srsAuchSpiele","srsKeineAK", "srsKeineEx", 
				"srsLigaNummer",
				"srsURL", "srsTabellenSpalten", "srsTabellenKopf", 
				"srsTabellenFormat"
				], function(i, val) {
				var m = val.match(/^srs(.*)/);
				if (m && o.attr(val)) {p[m[1].toLowerCase()] = o.attr(val); }
				val = "data-" + val;
				if (m && o.attr(val)) {p[m[1].toLowerCase()] = o.attr(val); }
			});
			if (p.title) m = "<p>"+p.title+"</p>"; else m = "";
			if (divs.length > 1 && links.length > 0) {
				m = "<a name=\"srs_a_"+i+"\"></a>" + m;
				anchors.push('<a href="#srs_a_'+i+'">'+(p.title?p.title:p.klasse?p.klasse:"Tab "+i)+'</a>');
			}
			o.html(m+"<p class=\"srsLaden\">" + (window.srsTabMsg || "Tabelle " + (p.klasse || p.liganummer || "")+ " wird geladen") + "</p>");
			$.ajax({
			    type: "GET",
			    url: window.location.hostname === "localhost" ? "fetch_table.php" : "http://handball-sr-mittelfranken.de/tabellen/fetch_table.php",
			    data : p, 
			    dataType: "jsonp",
			    success: function(data, textstatus) {
				show_tables(div, data, p);
			    },
			    error: function(XMLHttpRequest, textStatus, errorThrown) {
				o.html("Error: " + textStatus);
			    }
			});
		});
		// Links setzen, wenn alle Ligen (ohne daten) im DOM sind
		if (anchors.length > 0) links.append(anchors.join(" | "));
	};
	// haben wir tabellen f�r alle mannschaften eines Vereins?
	var v = $("div.srsTabVerein");
	if (v.length) {
		// wenn ja, ersetzen durch alle einzelnen Tabellen f�r diesen Verein
		v.each(function() {
			var o = $(this);
			var p = {};
			// get all attributes srs*
			$.each([
				"srsVerein", "srsTitle", "srsMinitab","srsAuchSpiele","srsKeineAK", "srsKeineEx", "srsAlle",
				"srsTabellenSpalten", "srsTabellenKopf", "srsTabellenFormat"
				], function(i, val) {
				var m = val.match(/^srs(.*)/);
				if (m && o.attr(val)) {p["srs"+m[1].toLowerCase()] = o.attr(val); }
				val = "data-" + val;
				if (m && o.attr(val)) {p["srs"+m[1].toLowerCase()] = o.attr(val); }
			});
			p.charset = document.charset || document.characterSet || "";
			$.ajax({
			    type: "GET",
			    url: window.location.hostname === "localhost" ? "tabsproverein.php" : "http://handball-sr-mittelfranken.de/tabellen/tabsproverein.php",
			    data : p, 
			    dataType: "jsonp",
			    success: function(data, textstatus) {
				if (!data) data = "<div>";
				o.replaceWith($(data));
				// wenn alle Vereinstabellen aufgel�st, die einzelnen Tabellen suchen und darstellen
				if ($("div.srsTabVerein").length == 0) lookup_tables();
			    },
			    error: function(XMLHttpRequest, textStatus, errorThrown) {
				o.html("Error: " + textStatus);
			    }
			});
		});
	} else {
		// wenn nein, einzelne Tabellen suchen und darstellen
		lookup_tables();
	}
	// haben wir Spielpl�ne f�r eine bestimmte Klasse und Verein
	// diese ausgeben als datum, zeit, halle, heim, gast
	$("div.srsPlan").each(function() {
		var o = $(this);
		var p = {};
		var m;
		// get all attributes srs*
		$.each([
			"srsTitle","srsVerein","srsGruppe","srsKlasse", "srsAlle", "srsHeimGast",
			"srsLigaNummer",
			"srsURL", "srsTabellenSpalten", "srsTabellenKopf", "srsVon", "srsBis", "srsAktuell",
			"srsTabellenFormat", "srsNurHalle"
			], function(i, val) {
			var m = val.match(/^srs(.*)/);
			if (m && o.attr(val)) {p[m[1].toLowerCase()] = o.attr(val); }
			val = "data-" + val;
			if (m && o.attr(val)) {p[m[1].toLowerCase()] = o.attr(val); }
		});
		if (!p.verein && !+p.alle && !+p.aktuell) {
			p.alle = "1";
			//o.html("Keinen Verein angegeben");
			//return;
		}
		if (p.title) m = "<p>"+p.title+"</p>"; else m = "";
		o.html(m+"<p class=\"srsLaden\">" + (window.srsPlanMsg || "Spielplan " + (p.klasse || p.liganummer || "") + " wird geladen") + "</p>");
		p.spielplan = 1;
		$.ajax({
		    type: "GET",
		    url: window.location.hostname === "localhost" ? "fetch_table.php" : "http://handball-sr-mittelfranken.de/tabellen/fetch_table.php",
		    data : p, 
		    dataType: "jsonp",
		    success: function(data, textstatus) {
			show_plan(o.get(), data, p);
		    },
		    error: function(XMLHttpRequest, textStatus, errorThrown) {
			o.html("Error: " + textStatus);
		    }
		});
	});
	// einen Plan als table ausgeben
	var show_plan = function(div, data, p) {
		var o = $(div);
		var d, dd;
		o.find(".srsLaden").remove();
		//if (data && data.error && data.error.$) {$(div).append(data.error.$); return;}
		if (data && data.Spielplan && data.Spielplan.Spielplan) {
			p.header = p.tabellenkopf || (p.spielplanverein ? "Datum;Zeit;Liga;Halle;Heim;Gast;Ergebnis" : "Datum;Zeit;Halle;Heim;Gast;Ergebnis");
			p.fields = p.tabellenspalten || (p.spielplanverein ? "Spieldatum/^..(..).(..).(..).*/$3.$2.$1;Spieldatum/.*T(..:..).*/$1;Liga_Name_kurz;Halle_Kuerzel;HeimTeam_Name_kurz;GastTeam_Name_kurz;Heim+%:+Gast" : "Spieldatum/^..(..).(..).(..).*/$3.$2.$1;Spieldatum/.*T(..:..).*/$1;Halle_Kuerzel;HeimTeam_Name_kurz;GastTeam_Name_kurz;Heim+%:+Gast");
			p.classCol = p.tabellenformat || (p.spielplanverein ? "l;c;l;l;l;l;c" : "l;c;l;l;l;c");
			// filtern nach von und bis
			if (data.Spielplan.von && data.Spielplan.bis && data.Spielplan.von.$ > '' && data.Spielplan.bis.$ > '') {
				dd = [];
				$.each(data.Spielplan.Spielplan, function(i, val) {
					if (val.SpieldatumTag.$ >= data.Spielplan.von.$ 
						&& val.SpieldatumTag.$ <= data.Spielplan.bis.$) 
						dd.push(val);
				});
				data.Spielplan.Spielplan = dd;
			}
			// filtern nach nach Halle
			if (p.nurhalle && p.nurhalle > '') {
				dd = [];
				var re = new RegExp(p.nurhalle.replace(",", "|"));
				$.each(data.Spielplan.Spielplan, function(i, val) {
					if (val.Halle_Kuerzel && val.Halle_Kuerzel.$ && val.Halle_Kuerzel.$.match(re)) 
						dd.push(val);
				});
				data.Spielplan.Spielplan = dd;
			}
			var h = create_table(p.header, p.fields, data.Spielplan.Spielplan);
			d = $(document.createElement("div"));
			d.html(h);
			if (!p.classtable) p.classtable = "srsTable";
			d.find("table").addClass(p.classtable);
			var t = d.find("tr");
			$.each(p.classCol.split(/;/), function (i, val) {
				t.find("td:eq("+i+")").addClass(val).end()
				 .find("th:eq("+i+")").addClass(val).end();
			});
			// eigenen Verein einf�rben
			if (p.verein) {
				t = d.find("tr:gt(0)");
				t.filter(":contains('"+p.verein+"')").addClass("srsHome");
				// rest l�schen
				if (!+p.alle || +p.heimgast) {
					d.find("tr:gt(0):not(.srsHome)").remove();
					d.find("tr.srsHome").removeClass("srsHome");
				}
				// heim und ausw (Spalte �ber format bestimmen)
				var sh = $.inArray("HeimTeam_Name_kurz", p.fields.split(/;/));
				var sa = $.inArray("GastTeam_Name_kurz", p.fields.split(/;/));
				t = d.find("tr:gt(0)");
				t.find("td:eq("+sh+"):contains('"+p.verein+"')").parent().addClass("srsHeim");
				t.find("td:eq("+sa+"):contains('"+p.verein+"')").parent().addClass("srsAusw");
				t.find("td:contains('"+p.verein+"')").addClass("srsTabHome");
				// nur heim oder gast stehen lassen
				if (+p.heimgast === 1) {
					d.find("tr:gt(0):not(.srsHeim)").remove();
				}
				if (+p.heimgast === 2) {
					d.find("tr:gt(0):not(.srsAusw)").remove();
				}
			}
			o.append(d);
		} else {
			o.append("<p>Keine Spiele gefunden, die den Suchbedingungen entsprechen</p>");
		}

	}
	// Gesamtspielplan f�r einen Verein
	$("div.srsPlanVerein").each(function() {
		var o = $(this);
		var p = {};
		var m;
		// get all attributes srs*
		$.each([
			"srsTitle","srsVerein","srsAlle", "srsHeimGast", "srsVon", "srsBis",
			"srsTabellenSpalten", "srsTabellenKopf", 
			"srsClub", "srsVerband",
			"srsTabellenFormat", "srsNurHalle"
			], function(i, val) {
			var m = val.match(/^srs(.*)/);
			if (m && o.attr(val)) {p[m[1].toLowerCase()] = o.attr(val); }
			val = "data-" + val;
			if (m && o.attr(val)) {p[m[1].toLowerCase()] = o.attr(val); }
		});
		if (!p.verein && !p.club) {
			o.html("Keinen Verein angegeben");
			return;
		}
		// sind meherere vereine/clubs zu mischen
		p.club = p.club || "";
		p.verein = p.verein || "";
		var clubs = p.club.split(",");
		var vereine = p.verein.split(",");
		var dataAll = [];
		p.club = clubs.shift();
		p.verein = vereine.shift();
		if (p.title) m = "<p>"+p.title+"</p>"; else m = "";
		o.html(m+"<p class=\"srsLaden\">" + (window.srsPlanMsg || "Gesamt-Spielplan " + (p.verein) + " wird geladen") + "</p>");
		p.spielplanverein = 1;
		var processPart = function() {
			$.ajax({
			    type: "GET",
			    url: window.location.hostname === "localhost" ? "fetch_table.php" : "http://handball-sr-mittelfranken.de/tabellen/fetch_table.php",
			    data : p, 
			    dataType: "jsonp",
			    success: function(data, textstatus) {
				    if (clubs.length > 0) {
					    if (data && data.Spielplan && data.Spielplan.Spielplan) {
						    dataAll = dataAll.concat(data.Spielplan.Spielplan);
					    }
					    p.club = clubs.shift();
					    //p.verein = vereine.shift();
					    processPart();
					    return;
				    }
				    if (dataAll.length) {
					    if (data && data.Spielplan && data.Spielplan.Spielplan) {
						    dataAll = dataAll.concat(data.Spielplan.Spielplan);
						    dataAll.sort(function(a,b) {
							    if (a.SpieldatumTag.$ < b.SpieldatumTag.$) return -1;
							    if (a.SpieldatumTag.$ > b.SpieldatumTag.$) return 1;
							    return 0;
						    });
						    data.Spielplan.Spielplan = dataAll;
					    }
				    }
				    p.alle = 0;
				    show_plan(o.get(), data, p);
			    },
			    error: function(XMLHttpRequest, textStatus, errorThrown) {
				    o.html("Error: " + textStatus);
			    }
			});
		}
		processPart();
	});
});
