<template>
    <div class="container-fluid calendar">
        <header>
            <h4 class="display-6 mb-4 text-center">{{currentMonth}} {{currentYear}}</h4>
            <div class="row d-none d-lg-flex p-1 text-white calendar-header">
                <h5 class="col-sm p-1 text-center">Monday</h5>
                <h5 class="col-sm p-1 text-center">Tuesday</h5>
                <h5 class="col-sm p-1 text-center">Wednesday</h5>
                <h5 class="col-sm p-1 text-center">Thursday</h5>
                <h5 class="col-sm p-1 text-center">Friday</h5>
                <h5 class="col-sm p-1 text-center">Saturday</h5>
                <h5 class="col-sm p-1 text-center">Sunday</h5>
            </div>
        </header>
        <div class="row border border-right-0 border-bottom-0">
            <div v-for="(calendarDate, index) in calendarDates" :id="'calendar_date'+index" class="day col-lg p-2 border border-left-0 border-top-0 text-truncate" :class="{'d-none d-sm-inline-block bg-light text-muted':calendarDate.disabled === true}">
                <h5 class="row align-items-center">
                    <b class="date col-1 text-muted">{{calendarDate.date_number}} {{calendarDate.month_name}}</b>
                    <b class="col d-lg-none text-center text-muted">{{calendarDate.day_name}}</b>
                    <span class="col-1"></span>
                </h5>
                <a v-for="gym_class in calendarDate.gym_classes" class="event d-block p-1 pl-2 pr-2 mb-sm-2 mb-lg-1 rounded text-truncate small bg-success text-white" title="Test Event 2">{{gym_class.start_time}} {{gym_class.gym_class_name}}</a>
            </div>
        </div>

<!--        <div v-if="displayModal">-->
<!--            <transition name="modal">-->
<!--                <div class="modal-mask">-->
<!--                    <div class="modal-wrapper">-->
<!--                        <div class="modal-dialog" role="document">-->
<!--                            <div class="modal-content">-->
<!--                                <div class="modal-header">-->
<!--                                    <h5 class="modal-title">Modal title</h5>-->
<!--                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                                        <span aria-hidden="true" @click="displayModal = false">&times;</span>-->
<!--                                    </button>-->
<!--                                </div>-->
<!--                                <div class="modal-body">-->
<!--                                    <p>Modal body text goes here.</p>-->
<!--                                </div>-->
<!--                                <div class="modal-footer">-->
<!--                                    <button type="button" class="btn btn-secondary" @click="displayModal = false">Close</button>-->
<!--                                    <button type="button" class="btn btn-primary">Save changes</button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </transition>-->
<!--        </div>-->
<!--        <button @click="displayModal = true">Click</button>-->


<!--        <div class="py-2">-->
<!--            <button class="btn btn-primary" data-bs-target="#myModal" data-bs-toggle="modal"> Bootstrap modal </button>-->
<!--            <div class="modal" id="myModal">-->
<!--                <div class="modal-dialog">-->
<!--                    <div class="modal-content">-->
<!--                        <div class="modal-header">-->
<!--                            <h5 class="modal-title">Modal title</h5>-->
<!--                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--                        </div>-->
<!--                        <div class="modal-body">-->
<!--                            <p>Modal body text goes here.</p>-->
<!--                        </div>-->
<!--                        <div class="modal-footer">-->
<!--                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
<!--                            <button type="button" class="btn btn-primary">Save changes</button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->


        <div class="container mt-5">
            <div>
                <b-button v-b-modal.modal-1>Show Modal</b-button>
                <b-modal id="modal-1" title="Vue Js Bootstrap Modal Example">
                    <p class="my-4">Content goes here...</p>
                </b-modal>
            </div>
        </div>


    </div>
</template>

<script>
    export default {
        data() {
            return {
                calendarDates: [],
                changeLineCalendarDateIndexes: [],
                reservations: [],
                currentMonth: null,
                currentYear: null,
                displayModal: false,
                modalGymClassId: null,
            }
        },
        mounted() {
            let today = new Date();

            this.currentMonth = today.toLocaleString('default', { month: 'long' });
            // this.currentMonth = today.toLocaleString('el-GR', { month: 'long' });
            this.currentYear = today.getFullYear();

            axios.get('/admin/reservations', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.reservations.push(value);
                    });
                })
                .catch((error) => {
                    // error.response.status Check status code
                    // for each errors -> display
                    console.log(error);
                    // console.log(error.response.data.errors['name'][0]);
                }).finally(() => {
                //Perform action in always
            });

            axios.get('/calendar', this.form)
                .then((results) => {
                    results.data.forEach((value, index) => {
                        this.calendarDates.push(value);

                        if ((index+1)%7 === 0) {
                            this.changeLineCalendarDateIndexes.push(index);
                        }
                    });
                })
                .catch((error) => {
                    // error.response.status Check status code
                    // for each errors -> display
                    console.log(error);
                    // console.log(error.response.data.errors['name'][0]);
                }).finally(() => {
                //Perform action in always
                this.changeLineCalendarDateIndexes.forEach((value, index) => {
                    let d1 = document.getElementById('calendar_date'+value);
                    d1.insertAdjacentHTML('afterend', '<div class="w-100"></div>');
                });
            });
        },
        methods:{
            showModal(gymClassId) {
                this.displayModal = true;
                this.modalGymClassId = gymClassId;
            },
            hideModal() {
                this.displayModal = false;
                this.modalGymClassId = null;
            },
            toggleModal() {
            // .classList.toggle("change");
            //     document.getElementById('calendar_date').displayModal = false;
            },


            // startTransitionModal() {
            //     vm.$refs.backdrop.classList.toggle("d-block");
            //     vm.$refs.modal.classList.toggle("d-block");
            // },
            // endTransitionModal() {
            //     vm.$refs.backdrop.classList.toggle("show");
            //     vm.$refs.modal.classList.toggle("show");
            // }
        }
    }
</script>
