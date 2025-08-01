jQuery(function($){
  // Densitets‐værdier pr. 1 dl (g)
  var densities = {
    "Boghvedemel":50,"Fuldkornsrismel":55,"Havremel":45,"Hirsemel":55,
    "Kikærtemel":50,"Majsmel":60,"Mandelmel":40,"Quinoamel":40,
    "Rismel":60,"Sorghummel":60,"Teffmel":50,
    "Boghvedeflager":35,"Fintvalsede havregryn":40,"Grovvalsede havregryn":40,
    "Hirseflager":50,"Quinoaflager":40,
    "Kartoffelmel":70,"Majsstivelse":50,"Tapiokastivelse":35,
    "Boghvedekerner":70,"Chiafrø":60,"Græskarkerner":55,"Hel hirse":70,
    "Hørfrø":55,"Knækket boghvede":60,"Majsgryn/polenta":60,"Quinoafrø":70,
    "Sesamfrø":50,"Solsikkekerner":55,
    "FiberHUSK":10,"Loppefrøskaller/psyllium":5,"Guargum":2,"Xanthangum":2.5
  };

  // Omregning ml ↔ enheder
  var unitToMl = { ml:1, cl:10, dl:100, l:1000, tsk:5, spsk:15 };

  // Formatér tal med komma og uden unødvendige nuller
  function fmt(n){
    return Math.abs(n - Math.round(n)) < 1e-9
      ? "" + Math.round(n)
      : n.toFixed(2).replace(/\.?0+$/,"").replace(".",",");
  }

  // Udfør beregning i én container
  function calculate($w){
    var ingrediens = $w.hasClass("single")
                   ? $w.data("name")
                   : $w.find(".ingredient").val();
    var fv = parseFloat($w.find(".from-value").val()) || 0;
    var fu = $w.find(".from-unit").val();
    var tu = $w.find(".to-unit").val();
    var d  = densities[ingrediens] / 100; // g pr. ml
    var result = 0;

    // Hvis input er volumen
    if (unitToMl[fu]) {
      var ml = fv * unitToMl[fu],
          g  = ml * d;
      if (tu === "kg")       result = g / 1000;
      else if (tu === "g")    result = g;
      else                    result = (g / d) / unitToMl[tu];
    }
    // Hvis input er vægt
    else {
      var g  = fv * (fu === "kg" ? 1000 : 1),
          ml = g / d;
      if (unitToMl[tu])      result = ml / unitToMl[tu];
      else                    result = (tu === "kg" ? g / 1000 : g);
    }

    $w.find(".result").text(fmt(result) + " " + tu);
  }

  // 1) Bind til ændringer
  $(document).on("input change",
    ".loevegaarden-beregner input, .loevegaarden-beregner select",
    function(){
      calculate($(this).closest(".loevegaarden-beregner"));
    }
  );

  // 2) Førsteberegning ved sideload
  $(".loevegaarden-beregner").each(function(){
    calculate($(this));
  });
});
