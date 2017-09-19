<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div :style="'width:' + width + 'px;'" class="modal-container">

                    <div class="modal-header">
                        <slot name="header">
                            default header
                        </slot>
                    </div>

                    <div class="modal-body">
                        <slot name="body">
                            default body
                        </slot>
                    </div>

                    <div class="modal-footer">
                        <slot name="footer">
                            <button class="modal-default-button" @click="$emit('close')">Close</button>
                            <button class="modal-default-button" @click="$emit('confirm')">OK</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/babel">

    export default {
        name: "modal",
        props:{
            width:{
                default: 400
            }
        },
        data(){
            return{

            }
        },

        methods: {

        }


    }
</script>

<style scoped>
.modal-mask {
  position: fixed;
  z-index: 10;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 400px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
  border-radius: 5px;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button:nth-child(2) {
    margin-left: 15px;
}



.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>