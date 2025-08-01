# Løvegården Mængde–Vægt Beregner

![Løvegården Logo](https://www.loevegaarden.dk/wp-content/uploads/2021/01/loevegaarden-logo.png)

## Beskrivelse

Omregn mellem volumen- og vægtenheder for mel, flager, stivelse, kerner og andet glutenfri bagetilbehør på en enkel og elegant måde.

Plugin’et inkluderer:

- Generel converter med dropdown for ingrediens + “Fra”/“Til”-vælger  
- Specifik converter med kun “Fra”/“Til” for en forudvalgt ingrediens  
- Styling i Løvegårdens mørkegrønne #234a1f  
- Automatisk fjernelse af `<br>`-tags ved indlæsning  

## Installation

1. Kopiér eller clone plugin-mappen til din WordPress-plugins-mappe:
   ```bash
   git clone https://github.com/dit-brugernavn/loevegaarden-beregner.git


## Brug
###Generel converter
Brug shortcoden uden parametre:
[loevegaarden_beregner]

###Specifik converter
Angiv name, from og/eller to for at lave en one-liner:

[loevegaarden_beregner name="Boghvedemel" from="l" to="g"]

Viser kun “Fra”-felt, unit-dropdown og “Til”-unit for den valgte ingrediens

Default: from="dl", to="g", value="1"


##Udvidelse
Vil du tilføje flere ingredienser?

Clone plugin’et og tilføj nye entries i PHP-arrayet $groups i loevegaarden-beregner.php.

Tilføj tilsvarende densitets-værdier i densities-objektet i js/loevegaarden-beregner.js (g pr. dl).

I fremtiden planlægger vi at give et admin-interface, så du kan redigere listerne uden at ændre kode.


##Links
Plugin-side: https://github.com/dit-brugernavn/loevegaarden-beregner

Løvegårdens hjemmeside: https://www.loevegaarden.dk