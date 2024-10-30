var component = Vue.component('mt_star_rating', {
    template:'<div class="mt_starRating"><div class="mt_starRating_title"><h3>{{ title }}</h3></div><div class="mt_starRating_stars_container"><div class="mt_starRating_stars"> <span v-for="n in max">&star;</span> <div class="mt_starRating_stars__note" :style="{width: getWidth}"> <span v-for="n in max">&starf;</span></div></div></div><div class="mt_starRating_value"><p>{{noteValue}} / {{max}}</p></div></div>',
    props: ['note_value', 'title', 'max', 'request_url'],
    data: function(){
      return {
        noteValue:'--'
      }
    },
    mounted: function(){
      if (isNaN(this.note_value)){
        var requestURL = this.request_url;
        var thisComponent = this;
        var request = jQuery.ajax({
            type: "POST",
            url: requestURL,
            dataType: "html",
            success: function(response) {
              var value = parseFloat(response);
              thisComponent.noteValue = value;
            },
            error: function (xhr, ajaxOptions, thrownError) {
            }
        })
      }
      else thisComponent.noteValue = this.note_value
    },
    computed: {
      getWidth: function() {
        return ( this.noteValue / this.max * 100 ) + '%';
      },
    },
});

var rootElements = jQuery('.mt_star_rating')
var rootElementsIDs = []
for (var i = 0; i < rootElements.length; i++) {
  if (typeof rootElements[i].id !== 'undefined') {
    rootElementsIDs.push(rootElements[i].id)
  }
}

var mtStarRating = {}

for (var i=0; i < rootElementsIDs.length; i++) {
  mtStarRating[id] = new Vue({
      el: '#'+rootElementsIDs[i],
  });
}

function StarsSizeManager(starsClass, starsContainerClass){

  defautFontSize = jQuery(starsClass).css('font-size');
  this.defaultFontSize = parseInt(defautFontSize, 10);

  this.manageStarsSize = function() {

    var starCount = jQuery(starsClass+'__note').children().length / jQuery(starsClass+'__note').length;

    var safeWidth = jQuery(starsContainerClass).outerWidth();
    var actualWidth = jQuery(starsClass).outerWidth();
    var defaultWidth = this.defaultFontSize*starCount ;

    function resizeToSafeWidth(){
      var safeFontSize = 0.99* safeWidth/starCount;
      jQuery(starsClass).css({fontSize:safeFontSize+'px'});
    }

    if ( actualWidth > safeWidth ){
      resizeToSafeWidth()
    }
    if ( (safeWidth > actualWidth) && (defaultWidth > actualWidth) ){
      resizeToSafeWidth()
    }
    else if ( safeWidth >= defaultWidth ) {
      jQuery(starsClass).css({fontSize:""})
    }
    else {
    }
  }

}

starSize = new StarsSizeManager('.mt_starRating_stars', '.mt_starRating_stars_container');
starSize.manageStarsSize();

var resizeSensors = {}

for (var id in rootElementsIDs) {
  resizeSensors[id] = new ResizeSensor(jQuery('#'+id), function() {
      starSize.manageStarsSize()
  });
}
