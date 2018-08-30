<!--
  - Copyright (C) 2018 Michael Milawski - All Rights Reserved
  - You may use, distribute and modify this code under the
  -  terms of the MIT license.
  -->

<template>
<div>

    <transition name="modal" v-if="showModal">
        <div class="modal-mask">
            <div class="modal-wrapper" @click.stop.capture="closeModal()">
                <div class="modal-container">

                    <div class="modal-header">
                        <slot name="header">
                            Job {{ job.id }} - {{job.context}}
                        </slot>
                    </div>

                    <div class="modal-body">
                        <slot name="body">

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Priority:</label> {{ job.priority }}
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Added</label> {{ job.time_added }}
                                </div>
                                <div class="col-md-4">
                                    <label for="">Completed</label> {{ job.time_completed }}
                                </div>
                                <div class="col-md-4">
                                    <label for="">Status:</label> {{ job.worker_status }}
                                </div>
                            </div>



                            <h3>Output</h3>
                            <div class="output">
                                {{job.output}}
                            </div>
                        </slot>
                    </div>

                    <div class="modal-footer">
                        <slot name="footer">
                            <button class="btn btn-primary modal-default-button" @click="closeModal()">
                                OK
                            </button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>

</div>
</template>

<script>
    export default {
        data () {
            return {
                job: {
                    output: "NONE"
                },
                showModal: false
            }
        },
        methods: {
                closeModal() {
                    this.showModal = false;
                }
            },

        mounted() {

            var th = this;
            this.$root.$on("job_modal_open", function(data){
                th.job = data.job;
                th.showModal = true;

            });


        }
    }
</script>