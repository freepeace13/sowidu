/*
 * Slim v4.16.4 - Image Cropping Made Easy
 * Copyright (c) 2018 Rik Schennink - http://slimimagecropper.com
 */
<template>
    <div id="cropContainerEyecandy" class="slim z-depth-1 responsive-img activator">
        <slot></slot>
    </div>
</template>

<style type="text/css">
#cropContainerEyecandy {
  height: 150px !important;
  width: 180px !important;
  margin-left:auto;
  margin-right:auto;
}
#cropContainerEyecandy .slim-result img {
  height: 150px !important;
  width: 180px !important;
  border: 2px solid #444;
  border-radius: 5px;
}
#cropContainerEyecandy .slim-btn-group {
  width: 70px;
  height: 200px;
  bottom: 130px;
  position: relative;
  left: -50px;
}
#cropContainerEyecandy .slim-btn-remove {
  bottom: 0px;
  left: -15px;
  position: relative;
}
#cropContainerEyecandy .slim-btn-edit {
  bottom: -5px;
  right: 15px;
  position: relative;
}

#cropContainerEyecandy .slim-btn-upload {
  position: absolute;
  top: 100px;
  left: -6px;
  display: none;
}
.slim .slim-file-hopper {
  cursor: pointer;
}
</style>

<script>
// Slim (place slim CSS and slim.module.js file in same folder as this file)
import Slim from "./slim.module.js";

var instance = null;

export default {
  props: ["options"],
  name: "slim-cropper",
  mounted: function() {
    if (this.options.initialImage) {
      var img = document.createElement("img");
      img.setAttribute("alt", "");
      img.src = this.options.initialImage;
      this.$el.appendChild(img);
    }
    instance = new Slim(this.$el, this.options);
  },
  destroyed: function() {
    instance.destroy();
  }
};
</script>
<style lang="css">
@import "./slim.min.css";
</style>
