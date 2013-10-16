(function ($) {
  function TransloaditUploader() {
    this.$videoForm      = null;
    this.videoUploadUrl  = null;

    this.thumbnailCount  = 10;
    this.thumbnailWidth  = 100;
    this.thumbnailHeight = 100;

    this.wasSubmitted    = false;
  }

  TransloaditUploader.prototype.init = function($container) {
    this.$videoForm  = $container.find('.js-video-upload-form');
    this.$finalForm  = $container.find('.js-final-upload-form');
    this.$thumbnails = $container.find('.js-thumbnails');

    this._bindTransloaditToVideoUploadForm();
    this._bindFinalFormSubmit();

    return this;
  };

  TransloaditUploader.prototype.reset = function() {
    this.$thumbnails.hide();

    this.$finalForm.find('input[type=text]').val('');
    this.$finalForm.hide();
    this.wasSubmitted = false;
  };

  TransloaditUploader.prototype._bindTransloaditToVideoUploadForm = function() {
    var self = this;

    this._fetchParamsAndSigStep1(function(params, signature) {
      self.$videoForm.transloadit({
        wait: true,
        autoSubmit: false,
        triggerUploadOnFileSelection: true,
        params: params,
        signature: signature,
        onStart: function() {
          self.reset();
        },
        onSuccess: function(assembly) {
          var thumbs = assembly.results.extract_thumbs;
          self._showThumbnails(thumbs);
          self.videoUploadUrl = assembly.uploads[0].url;
          self.$finalForm.show();
        }
      });
    });
  };

  TransloaditUploader.prototype._showThumbnails = function(thumbs) {
    this.$thumbnails.empty();

    for (var i = 0; i < thumbs.length; i++) {
      var thumb = thumbs[i];
      var $img = $('<img />');
      $img
        .attr('src', thumb.url)
        .attr('width', this.thumbnailWidth)
        .attr('height', this.thumbnailHeight)
        .attr('class', 'img-thumbnail');

      var $radio = $('<input/>');
      $radio
        .attr('type', 'radio')
        .attr('name', 'chosen_thumbnail')
        .attr('rel', thumb.url);

      if (i === 0) {
        $radio.attr('checked', 'checked');
        this.chosenThumbUrl = $radio.attr('rel');
      }

      var $li = $('<li/>').append($radio).append($img);
      this.$thumbnails.append($li);
    }

    this._bindThumbnailSelection();
    this.$thumbnails.show();
  };

  TransloaditUploader.prototype._bindThumbnailSelection = function() {
    var $radios = this.$thumbnails.find('input:radio');
    var self = this;

    $radios.off('change').on('change', function() {
      self.chosenThumbUrl = $(this).attr('rel');
    });
  };

  TransloaditUploader.prototype._bindFinalFormSubmit = function() {
    var self = this;

    this.$finalForm.on('submit', function(e) {
      if (!self.wasSubmitted) {
        e.preventDefault();
        self._bindFinalFormTransloadit(function() {
          self.$finalForm.trigger('submit.transloadit');
          self.wasSubmitted = true;
        });
      }
    });
  };

  TransloaditUploader.prototype._bindFinalFormTransloadit = function(cb) {
    var self = this;

    this._fetchParamsAndSigStep2(function(params, signature) {
      self.$finalForm.transloadit({
        wait: true,
        params: params,
        signature: signature
      });

      cb();
    });
  };

  TransloaditUploader.prototype._fetchParamsAndSigStep1 = function(cb) {
    var query = 'step=1';
    query += '&w=' + this.thumbnailWidth;
    query += '&h=' + this.thumbnailHeight;
    query += '&c=' + this.thumbnailCount;
    $.getJSON('params.php?' + query, function(response) {
      cb(response.params, response.signature);
    });
  };

  TransloaditUploader.prototype._fetchParamsAndSigStep2 = function(cb) {
    var query = 'step=2';
    query += '&thumb_url=' + encodeURIComponent(this.chosenThumbUrl);
    query += '&video_url=' + encodeURIComponent(this.videoUploadUrl);
    $.getJSON('params.php?' + query, function(response) {
      cb(response.params, response.signature);
    });
  };

  $.fn.transloaditUpload = function() {
    var obj = (new TransloaditUploader()).init(this);
    return this.data('transloaditUploader', obj);
  };

  $(function() {
    $('.js-transloadit-upload').each(function() {
      $(this).transloaditUpload();
    });
  });
})(jQuery);
