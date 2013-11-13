// slide.js: Modification of collapse.js

/* ========================================================================
 * Bootstrap: collapse.js v3.0.0
 * http://getbootstrap.com/javascript/#collapse
 * ========================================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */


+function ($) { "use strict";

  // SLIDE PUBLIC CLASS DEFINITION
  // ================================

  var Slide = function (element, options) {
    this.$element      = $(element)
    this.options       = $.extend({}, Slide.DEFAULTS, options)
    this.transitioning = null

    if (this.options.parent) this.$parent = $(this.options.parent)
    if (this.options.toggle) this.toggle()
  }

  Slide.DEFAULTS = {
    toggle: true
  }


  Slide.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('in')) return

    var startEvent = $.Event('show.bs.slide')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var outerWidth = this.$element.outerWidth()

    this.$element
      .removeClass('slide')
      .addClass('sliding')
      .css("left", (-outerWidth) + "px")

    this.transitioning = 1

    var complete = function () {
      this.$element
        .removeClass('sliding')
        .addClass('in')
        .css("left", 0)
      this.transitioning = 0
      this.$element.trigger('shown.bs.slide')
    }

    if (!$.support.transition) return complete.call(this)

    this.$element
      .one($.support.transition.end, $.proxy(complete, this))
      .emulateTransitionEnd(350)
      .css("left", 0)
  }

  Slide.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('in')) return

    var startEvent = $.Event('hide.bs.slide')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var outerWidth = this.$element.outerWidth()

    this.$element
      .addClass('sliding')
      .removeClass('slide')
      .removeClass('in')

    this.transitioning = 1

    var complete = function () {
      this.transitioning = 0
      this.$element
        .trigger('hidden.bs.slide')
        .removeClass('sliding')
        .addClass('slide')
    }

    if (!$.support.transition) return complete.call(this)

    this.$element
      .css("left", (-outerWidth) + "px")
      .one($.support.transition.end, $.proxy(complete, this))
      .emulateTransitionEnd(350)
  }

  Slide.prototype.toggle = function () {
    this[this.$element.hasClass('in') ? 'hide' : 'show']()
  }


  // SLIDE PLUGIN DEFINITION
  // ==========================

  var old = $.fn.slide

  $.fn.slide = function (option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.slide')
      var options = $.extend({}, Slide.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.slide', (data = new Slide(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.slide.Constructor = Slide


  // SLIDE NO CONFLICT
  // ====================

  $.fn.slide.noConflict = function () {
    $.fn.slide = old
    return this
  }


  // SLIDE DATA-API
  // =================

  $(document).on('click.bs.slide.data-api', '[data-toggle=slide]', function (e) {
    var $this   = $(this), href
    var target  = $this.attr('data-target')
        || e.preventDefault()
        || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
    var $target = $(target)
    var data    = $target.data('bs.slide')
    var option  = data ? 'toggle' : $this.data()

    if (!data || !data.transitioning) {
      $this[$target.hasClass('in') ? 'addClass' : 'removeClass']('slided')
    }

    $target.slide(option)
  })

}(window.jQuery);
