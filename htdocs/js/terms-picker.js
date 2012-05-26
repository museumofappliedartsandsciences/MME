function BlockTerms( q, callback ) {

  var self = this;

  this.q = q;
  this.callback = callback;
  this.id = false;

  var block = Blocker();

  this.ui = {};
  
  this.el = block.el;

  this.terms = [];

  this.render = function() {
    
    self.el.html('');

    var f = $('<form />')
      .appendTo(self.el)
      .bind('submit', function(e) {
        e.preventDefault();
        var q = $(this).find('[name=query]').val();
        if ( q != '' ) {
          self.id = false;
          self.q = q;
          self.query();
        } else if ( self.id ) {
          self.query();
        }
      });

    self.ui.query = $('<input type="text" name="query" />')
      .val(self.q)
      .appendTo(f)
      .select();

    $('<input type="submit" value="Search" />')
      .appendTo(f);

    $('<input type="button" class="cancel" value="Cancel" />')
      .appendTo(f)
      .bind('click', function(e) {
        e.preventDefault();
        block.el.html('');
        block.unblock();
        self.callback( false );
      });

    self.ui.terms = $('<div class="terms" />')
      .appendTo(self.el)
      .delegate('a.search', 'click', function(e) {
        e.preventDefault();
        var s = ( e.target.tagName =='SPAN' ) ? $(e.target).html() : $(e.target).find('span').html();
        self.ui.query.val(s);
        self.q = s;
        self.id = false;
        self.query();
      })
      .delegate('a.id', 'click', function(e) {
        e.preventDefault();
        var s = ( e.target.tagName =='SPAN' ) ? $(e.target).html() : $(e.target).find('span').html();
        self.ui.query.val(s);

        var href = ( e.target.tagName =='SPAN' ) ? $(e.target).parent().attr('href') : $(e.target).attr('href');
        var path = href.split('/');
        var id = path[path.length-1];
        self.q = s;
        self.id = id;
        self.query();
      });
    self.update();
  }

  this.update = function() {

	  self.el.find('[name=query]').val(self.q)

    self.ui.terms.html('');

    if ( self.terms.length == 1 ) {
      for ( var i in self.terms ) {
        if ( self.terms[i].status =='APPROVED' ) {
          self.ui.query.val(self.terms[i].term);
        }
      }
    }

    for ( var i in self.terms ) {
      var x = self.terms[i];

      var d = $('<div class="term" />')
        .appendTo(self.ui.terms)
        .addClass(x.status.toLowerCase());

      var h = $('<h3 />')
        .html( '<a href="/terms/' + x.id + '-' + slugify ( x.term ) + '"><span>' + x.term + '</span></a>' )
        .appendTo(d);
	  
      // $('<span class="status" />')
      //   .html( x.status )
      //   .prependTo(h);

      if ( x.status =='APPROVED' ) {

		  h.find('a').bind('click', function(e) {
				  e.preventDefault();
				  block.el.html('');
				  block.unblock();
			self.callback($(this).find('span').text());
			  });
		  
		  self.ui.query = $('<input type="button" name="' + x.term + '" value="Use This Term" />')
			  .appendTo(h)
			  .bind('click', function(e) {
					  e.preventDefault();
					  block.el.html('');
					  block.unblock();
				self.callback( $(this).attr('name'));
				  });
      } else {
		  h.find('a').bind('click', function(e) {
				  e.preventDefault();
				  self.q = $(e.target).text();
				  self.query();
            });
   	  }

      if ( x.scope_notes ) {
        $('<p class="notes" />')
          .html( x.scope_notes || '' )
          .appendTo(d);
      }

      if ( x.relations ) {
        if ( x.relations.use ) {
          var p = $('<p class="relation" />')
            .html('<strong>Use:</strong>').appendTo(d);
          $('<a href="/terms/' + x.relations.use.id + '-' + slugify ( x.relations.use.term ) + '" />')
            .html('<span>' + x.relations.use.term + '</span></a>')
            .appendTo(p)
            .bind('click', function(e) {
              e.preventDefault();
              self.q = x.relations.use.term;
              self.query();
            });
          $('<p />')
            .html( x.relations.use.scope_notes )
            .appendTo(d);
        }

        if ( x.relations.broader && x.relations.broader.length > 0 ) {
          var p = $('<p class="relation" />')
            .html('<strong>Broader:</strong>')
            .appendTo(d);
          _.each(x.relations.broader, function(q){
            $('<a href="/terms/' + q.id + '-' + slugify ( q.term ) + '" />' )
              .html('<span>' + q.term + '</span>')
              .appendTo(p)
              .bind('click', function(e) {
                e.preventDefault();
                self.q = q.term;
                self.query();
              });
          });
        } else if ( x.relations.broader ) {
          var t = x.relations.broader.term;
          var p = $('<p class="relation" />').appendTo(d);
          $('<strong>Broader:</strong>').appendTo(p);
          $('<a href="/terms/' + x.relations.broader.id + '-' + slugify ( x.relations.broader.term ) + '" />')
            .html('<span>' + x.relations.broader.term + '</span>' )
            .appendTo(p)
            .bind('click', function(e) {
              e.preventDefault();
              self.q = t;
              self.query();
            });
          
        }

        if ( x.relations.related ) {
          var p = $('<p class="relation" />');
          $('<strong>Related:</strong>').appendTo(p);
          $('<a href="/terms/' + x.relations.related.id + '-' + slugify ( x.relations.related.term ) + '">')
            .html('<span>' + x.relations.related.term + '</span></a>' )
            .appendTo(p)
            .bind('click', function(e) {
              e.preventDefault();
              self.q = x.relations.related.term;
              self.query();
            });
        }

        if ( x.relations.narrower.length > 0 ) {
          var p = $('<p class="relation" />')
            .html('<strong>Narrower:</strong>')
            .appendTo(d);
          _.each(x.relations.narrower, function(q){
            $('<a href="/terms/' + q.id + '-' + slugify ( q.term ) + '" />' )
              //.addClass('id')
              .html('<span>' + q.term + '</span>')
              .appendTo(p)
              .bind('click', function(e) {
                e.preventDefault();
                self.q = q.term;
                self.query();
              });

          });
        }

      }

    }


  }

  this.query = function() {
    if ( self.id ) {
      $.getJSON ('/terms?id=' + self.id, function(r) {
        self.terms = [];
        if ( r.status == 200 ) {
          self.terms.push(r.term);
        }
        self.update();
      });
    } else {
      $.getJSON( '/terms?q=' + self.q, function(r){
        self.terms = [];
        if ( r.status == 200 ) {
          self.terms = r.terms;
        }
        self.update();
      });
    }
  }

  this.render();
  this.query();
  
}

function slugify(text) {
  text = text.toLowerCase();
  text = text.replace(/[^-a-zA-Z0-9,&\s]+/ig, '');
  text = text.replace(/-/gi, "_");
  text = text.replace(/\s/gi, "-");
  return text;
}



function Blocker ( options ) {

  var self = this;
  self.options = options || {};
  self.options.addClass = self.options.addClass || {};

  self.options.close = self.options.close || function(){};

  self.block = $('<div id="blocker" />')
    .appendTo('body')
    .css( {'height': $(window).height() } );

  if ( self.options.modal === undefined || ! self.options.modal ) {
    self.block.bind('click', function () {
      self.unblock();
    });
  }

  this.el = $('<div id="blocker-panel" />').appendTo('body');

  if ( self.options.addClass ) {
    this.el.addClass ( self.options.addClass );
  }

  if ( self.options.click_to_close ) {
    this.el.bind('click', function () {
      self.unblock();
    });
  }

  this.center = function () {
    $('#blocker').css({"top": $(window).scrollTop(), "width": $(window).width(), "height": $(window).height() });
    self.el.css( { "top": $(window).scrollTop() + ( ( $(window).height() - self.el.height() ) / 4 ), "left": (($(window).width() - self.el.width())/2)  + 'px' });
  }

  this.center();

  var resizeTimer = null;

  $(window).bind('resize', function() {
    if (resizeTimer) {
      clearTimeout(resizeTimer);
    }
    resizeTimer = setTimeout( self.center, 100 );
  });

  $(window).bind('scroll', function() {
    if (resizeTimer) {
      clearTimeout(resizeTimer);
    }
    resizeTimer = setTimeout( self.center, 10 );
  });

  this.unblock = function() {
    self.options.close();
    $('#blocker').hide();
    $('#blocker-panel').fadeOut(450, function() {
      $('#blocker-panel').remove();
      $('#blocker').remove();
    });
    
  }

  return {
    el: self.el,
    unblock: function () { 
      self.unblock(); 
    },
    setsize: function (x, y) { 
      self.el.css({'width': x + 'px', 'height': y + 'px'});
      self.center();
    }

  }

}
