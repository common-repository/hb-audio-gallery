if ($("#hbag-map").length > 0) {
  // initializing new map
  const map = L.map("hbag-map", {
    center: [80.9098, 41.2284],
    zoom: 2,
    minZoom: 1,
    maxZoom: 17,
    maxBoundsViscosity: 1.0,
    maxBounds: [
      [90, -180],
      [-90, 180],
    ],
  });

  const tileUrl =
    "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png";
  const attribution =
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, HB Audio Gallery Plugin</a>';
  const tileLayer = L.tileLayer(tileUrl, { attribution, noWrap: true });
  tileLayer.addTo(map);

  //creating new geojson array by merging country specific audio count with coordinates
  var i = 0;
  var newGeoJson = { type: "FeatureCollection", features: [] };

  $.ajax({
    url: hb_object.ajaxUrl,
    method: "POST",
    async: false,
    dataType: "json",
    beforeSend: function () {
      $("#countryMapLoader").show();
    },
    data: {
      action: "get_total_audios_count",
    },
    success: function (response) {
      if (response) {
        for (const [countryName, audioCount] of Object.entries(response)) {
          countriesGeoJsonData.features.every((geoJsonCountry) => {
            if (countryName === geoJsonCountry.properties.name) {
              geoJsonCountry.properties.count = audioCount;
              newGeoJson.features.push(geoJsonCountry);
              return false;
            }
            return true;
          });
        }
      }
      $("#countryMapLoader").hide();
    },
  });

  L.geoJson(newGeoJson).addTo(map);

  // styling map with colors and highlight feature
  function getColor(d) {
    return d > 1000
      ? "#800026"
      : d > 500
      ? "#BD0026"
      : d > 200
      ? "#E31A1C"
      : d > 100
      ? "#FC4E2A"
      : d > 50
      ? "#FD8D3C"
      : d > 20
      ? "#FEB24C"
      : d > 10
      ? "#FED976"
      : "#FFEDA0";
  }

  function style(feature) {
    return {
      fillColor: getColor(feature.properties.count),
      weight: 2,
      opacity: 1,
      color: "white",
      dashArray: "3",
      fillOpacity: 0.7,
    };
  }

  var geoJson;
  function highlightFeature(e) {
    var layer = e.target;

    layer.setStyle({
      weight: 2,
      color: "#666",
      dashArray: "",
      fillOpacity: 0.7,
    });

    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
      layer.bringToFront();
    }

    // shows popup on mouse over
    var popupContent = `<h3>${e.sourceTarget.feature.properties.name}: ${e.sourceTarget.feature.properties.count}</h3>`;
    layer.bindPopup(popupContent);
    this.openPopup();
  }

  // hide popup on mouse out
  function resetHighlight(e) {
    geojson.resetStyle(e.target);
    this.closePopup();
  }

  function zoomToFeature(e) {
    map.fitBounds(e.target.getBounds());
  }

  // attaching popup on each feature
  function onEachFeature(feature, layer) {
    layer.on({
      mouseover: highlightFeature,
      mouseout: resetHighlight,
      click: zoomToFeature,
    });
  }

  geojson = L.geoJson(newGeoJson, {
    style: style,
    onEachFeature: onEachFeature,
  }).addTo(map);

  // adding legend info on bottom right
  var legend = L.control({ position: "bottomright" });

  legend.onAdd = function (map) {
    var div = L.DomUtil.create("div", "hbag-info hbag-legend"),
      grades = [0, 10, 20, 50, 100, 200, 500, 1000],
      labels = [];

    // loop through the audio count intervals and generate a label with a colored square for each interval
    for (var i = 0; i < grades.length; i++) {
      div.innerHTML +=
        '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' + 
        grades[i] + (grades[i + 1] ? "&ndash;" + grades[i + 1] + "<br>" : "+");
    }

    return div;
  };

  legend.addTo(map);
}
