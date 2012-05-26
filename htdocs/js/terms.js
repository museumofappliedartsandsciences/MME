$(function(){
  var terms = new Terms();
});


function Terms() {

  var self = this;
  this.ui = {};

  this.el = $('#terms');

  this.q = '';
  this.id = false;
  this.terms = [];

  // if ( window.location.hash ) {
  //   this.id = window.location.hash.substring(1);
  // } else {

  this.id = this.el.attr('data-id');
  if ( this.id == '' ) {
     this.q = this.el.attr('data-query');
  }

//}

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
    self.ui.terms.html('');

    if ( self.terms.length == 1 ) {
      for ( var i in self.terms ) {
        if ( self.terms[i].status =='APPROVED' ) {
          //window.location.hash = self.terms[i].id;
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

      $('<span class="status" />')
        .html( x.status )
        .prependTo(h);

      
      if ( x.scope_notes ) {
        $('<p class="notes" />')
          .html( x.scope_notes || '' )
          .appendTo(d);
      }

      if ( x.relations ) {
        console.log ( x );
        if ( x.relations.use ) {
          $('<h4 />')
            .html( 'Use: ' + '<a href="/terms/' + x.relations.use.id + '-' + slugify ( x.relations.use.term ) + '"><span>' + x.relations.use.term + '</span></a>' )
            .appendTo(d);

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
              .appendTo(p);
          });
        } else if ( x.relations.broader ) {
          var p = $('<p class="relation" />')
            .html('<strong>Broader:</strong> <a href="/terms/' + x.relations.broader.id + '-' + slugify ( x.relations.broader.term ) + '"><span>' + x.relations.broader.term + '</span></a>' ).appendTo(d);
        }

        if ( x.relations.related ) {
          var p = $('<p class="relation" />')
            .html('<strong>Related:</strong> <a href="/terms/' + x.relations.related.id + '-' + slugify ( x.relations.related.term ) + '"><span>' + x.relations.related.term + '</span></a>' ).appendTo(d);
        }

        if ( x.relations.narrower.length > 0 ) {
          var p = $('<p class="relation" />')
            .html('<strong>Narrower:</strong>')
            .appendTo(d);
          _.each(x.relations.narrower, function(q){
            $('<a href="/terms/' + q.id + '-' + slugify ( q.term ) + '" />' )
              //.addClass('id')
              .html('<span>' + q.term + '</span>')
              .appendTo(p);
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
  $('form').submit();
  
}

function slugify(text) {
  text = text.toLowerCase();
  text = text.replace(/[^-a-zA-Z0-9,&\s]+/ig, '');
  text = text.replace(/-/gi, "_");
  text = text.replace(/\s/gi, "-");
  return text;
}
