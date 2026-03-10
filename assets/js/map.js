jQuery(function ($) {

  function initLeafletWidget($root) {
    $root.find('.custom-leaflet-widget').each(function () {
      var $widget = $(this);
      var raw = $widget.attr('data-leaflet-config');
      if (!raw) return;

      var cfg;
      try { cfg = JSON.parse(raw); } catch (e) { return; }

      var mapEl = document.getElementById(cfg.mapId);
      if (!mapEl || !window.L) return;

      // Evita doppia init (Elementor editor / ajax)
      if (mapEl.dataset.inited === '1') return;
      mapEl.dataset.inited = '1';

      // Center iniziale: primo punto
      var first = cfg.points[0];
      var map = L.map(cfg.mapId, { scrollWheelZoom: false }).setView([first.lat, first.lng], cfg.zoom);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      var defaultIcon = L.icon({
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41]
      });

      var pulseIcon = L.divIcon({
        className: 'leaflet-pulse-marker',
        iconSize: [20, 20]
      });

      var markers = {};
      var bounds = [];

      cfg.points.forEach(function (p) {
        var icon = defaultIcon;
        var m = L.marker([p.lat, p.lng], { icon: icon }).addTo(map);

        if (cfg.showPopup) {
          m.bindPopup(p.title || '');
        }

        markers[p.id] = m;
        bounds.push([p.lat, p.lng]);
      });

      if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [30, 30] });
      }

      function setActive(pointId) {
        // lista active
        $widget.find('.custom-leaflet-item').removeClass('is-active');
        $widget.find('.custom-leaflet-item[data-point-id="' + pointId + '"]').addClass('is-active');

        // marker active (pulse o popup)
        Object.keys(markers).forEach(function (id) {
          if (cfg.pulseMarker) {
            markers[id].setIcon(defaultIcon);
          }
          markers[id].closePopup && markers[id].closePopup();
        });

        var m = markers[pointId];
        if (!m) return;

        if (cfg.pulseMarker) {
          m.setIcon(pulseIcon);
        }

        var p = cfg.points.find(x => x.id === pointId);
        if (p) {
          map.flyTo([p.lat, p.lng], cfg.activeZoom, { animate: true, duration: 0.6 });
          if (cfg.showPopup && m.openPopup) m.openPopup();
        }
      }

      // Bind eventi lista
      var evt = (cfg.interaction === 'click') ? 'click' : 'mouseenter';

      $widget.on(evt, '.custom-leaflet-item', function (e) {
        if (evt === 'click') e.preventDefault();
        setActive($(this).data('point-id'));
      });

      // click sempre come fallback mobile
      if (cfg.interaction !== 'click') {
        $widget.on('click', '.custom-leaflet-item', function (e) {
          e.preventDefault();
          setActive($(this).data('point-id'));
        });
      }
    });
  }

  // frontend
  initLeafletWidget($(document));

  // Elementor editor
  if (window.elementorFrontend && elementorFrontend.hooks) {
    elementorFrontend.hooks.addAction('frontend/element_ready/leaflet_points.default', function ($scope) {
      initLeafletWidget($scope);
    });
  }
});
