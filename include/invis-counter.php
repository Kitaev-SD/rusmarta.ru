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
            cbk_script.setAttribute("src", "https://cdn.envybox.io/widget/cbk.js?wcb_code=18a8eb94294338168d5d7637a6ff24a2"); 
            cbk_script.setAttribute("charset", "UTF-8");
            cbk_script.setAttribute("async", "");
            
            var cbk_link = document.createElement('link');
            cbk_link.setAttribute("rel", "stylesheet");
            cbk_link.setAttribute("href", "https://cdn.envybox.io/widget/cbk.css");
            
            document.body.appendChild(cbk_script);
            document.body.appendChild(cbk_link);
            
            //------------------------------------//

            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(22745254, "init", { 
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true,
                    trackHash:true 
            });
            //------------------------------------//

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