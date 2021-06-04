<!-- Yandex.Metrika counter -->
<script async type="text/javascript">
  $(document).ready(function(){

    var fired = false;

    window.addEventListener('click', () => {
        if (fired === false) {
            fired = true;
            load_other();
      }
    });
 
    window.addEventListener('scroll', () => {
        if (fired === false) {
            fired = true;
            load_other();
      }
    });

    window.addEventListener('mousemove', () => {
        if (fired === false) {
            fired = true;
            load_other();
      }
    });

    window.addEventListener('touchmove', () => {
        if (fired === false) {
            fired = true;
            load_other();
      }
    });

    function load_other() {
      
        setTimeout(function() {
            //------------------------------------//
            var cbk_script = document.createElement('script');
            cbk_script.setAttribute("type", "text/javascript"); 
            cbk_script.setAttribute("src", "https://cdn.envybox.io/widget/cbk.js?wcb_code=18a8eb94294338168d5d7637a6ff24a2&v=1"); 
            cbk_script.setAttribute("charset", "UTF-8");
            cbk_script.setAttribute("async", "");

            var googletagmanager_script = document.createElement('script');
            googletagmanager_script.setAttribute("type", "text/javascript"); 
            googletagmanager_script.setAttribute("src", "https://www.googletagmanager.com/gtag/js?id=UA-59688382-1&v=1"); 
            googletagmanager_script.setAttribute("charset", "UTF-8");
            googletagmanager_script.setAttribute("async", "");
            
            var cbk_link = document.createElement('link');
            cbk_link.setAttribute("rel", "stylesheet");
            cbk_link.setAttribute("href", "https://cdn.envybox.io/widget/cbk.css");
            
            document.body.appendChild(cbk_script);
            document.body.appendChild(cbk_link);
            
            //------------------------------------//

            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js?v=1", "ym");

            ym(22745254, "init", { 
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true,
                    trackHash:true 
            });
            //------------------------------------//

            // <!-- Google Tag Manager -->

          (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
              'gtm.start': new Date().getTime(),
              event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
              j = d.createElement(s),
              dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
              'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
          })(window, document, 'script', 'dataLayer', 'GTM-M32PPZL');

          // <!-- End Google Tag Manager -->

          // <!-- code tag Google analytics -->

          (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
              (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
              m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
          })(window, document, 'script', 'https://www.google-analytics.com/analytics.js?v=1', 'ga');

          ga('create', 'UA-59688382-1', 'auto');
          ga('send', 'pageview');

          // <!-- Global site tag (gtag.js) - Google Analytics -->

          window.dataLayer = window.dataLayer || [];

          function gtag() {
          dataLayer.push(arguments);
          }
          gtag('js', new Date());

          gtag('config', 'UA-59688382-1');
           
      }, 100);
    }
  });
</script>

<noscript><div><img src="https://mc.yandex.ru/watch/22745254" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M32PPZL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->






	

