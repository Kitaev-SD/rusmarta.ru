$().ready(function() {
    $('.plus-minus').click(function(event) {
	event.preventDefault();
        $(this).parents('.parent').next('ul').slideToggle();
        $(this).toggleClass("up down");
    });

    $('.content__left_menu__header').on('click', function(e) {
        $('#vertical-multilevel-menu').toggleClass('active');
    });
});
var jsvhover = function()
{
	var menuDiv = document.getElementById("vertical-multilevel-menu");
	if (!menuDiv)
		return;

  var nodes = menuDiv.getElementsByTagName("li");
  for (var i=0; i<nodes.length; i++) 
  {
    nodes[i].onmouseover = function()
    {
      this.className += " jsvhover";
    }
    
    nodes[i].onmouseout = function()
    {
      this.className = this.className.replace(new RegExp(" jsvhover\\b"), "");
    }
  }
}

if (window.attachEvent) 
	window.attachEvent("onload", jsvhover);
