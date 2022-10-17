<template>
    <div class="wave-content-padding-y">
        <div class="col-12 col-lg-11 m-auto p-4">
            <div class="container-fluid student-calendar">
                <header>
                    <div class="row d-none d-lg-flex p-1 text-white student-calendar-header">
                        <b class="col-sm p-1 text-center">Monday</b>
                        <b class="col-sm p-1 text-center">Tuesday</b>
                        <b class="col-sm p-1 text-center">Wednesday</b>
                        <b class="col-sm p-1 text-center">Thursday</b>
                        <b class="col-sm p-1 text-center">Friday</b>
                        <b class="col-sm p-1 text-center">Saturday</b>
                        <b class="col-sm p-1 text-center">Sunday</b>
                    </div>
                </header>

                <!-- loader -->
                <div v-if="isLoading" class="row border p-4">
                    <b-spinner class="spinner-size-default" variant="info"></b-spinner>
                </div>

                <!-- calendar dates -->
                <div v-if="!isLoading" class="row border border-right-0 border-bottom-0 student-calendar-content">
                    <div v-for="(calendarDate, index) in calendarDates" :id="'calendar_date'+index" class="day col-lg p-3 p-md-2 border border-left-0 border-top-0 text-truncate" :class="[{'d-none d-sm-inline-block bg-light student-calendar-day-muted':calendarDate.disabled === true}, {'hide-date':calendarDate.gym_classes.length === 0}, {'student-calendar-day-border-right-0':changeLineCalendarDateIndexes.includes(index)}]">
                        <h5 class="row align-items-center pb-3">
                            <b class="date col-1">{{calendarDate.date_number}} {{calendarDate.month_name}}</b>
                            <b class="col d-lg-none text-center">{{calendarDate.day_name}}</b>
                            <span class="col-1"></span>
                        </h5>
                        <a v-for="gym_class in calendarDate.gym_classes" @click="buildModal(calendarDate, gym_class)" v-b-modal.modal-student-calendar class="event d-block p-1 pl-2 pr-2 mb-2 rounded text-truncate small bg-success text-white" :title="gym_class.gym_class_name">
                            {{gym_class.start_time}} {{gym_class.gym_class_name}}
                            <font-awesome-icon v-if="gym_class.user.has_active_reservation === true" icon="fa-solid fa-circle-check" class="reservation-status reservation-status-reserved"/>
                            <font-awesome-icon v-if="gym_class.user.declined === true" icon="fa-solid fa-circle-xmark" class="reservation-status reservation-status-declined"/>
                        </a>
                    </div>
                </div>

                <div class="container mt-5">

                    <!--Show reservations modal-->
                    <b-modal id="modal-student-calendar">
                        <template #modal-header="{ close }">
                            <b>{{modalGymClass.gym_class_name}}</b>
                            <!-- Emulate built in modal header close button action -->
                            <span class="modal-header-close-button" @click="close()">×</span>
                        </template>

                        <div class="mb-4">
                            <div class="modal-info mb-2">
                                <b>
                                    <i>{{modalTitle}} | {{modalGymClass.start_time}} - {{modalGymClass.end_time}}</i>
                                    <b class="float-right me-2">{{modalGymClass.number_of_reservations}}/{{modalGymClass.number_of_students_limit}}</b>
                                    <br>
                                    <i v-if="modalGymClass.teacher">{{modalGymClass.teacher}}</i>
                                </b>
                            </div>

                            <p>{{modalGymClass.description}}</p>
                        </div>

                        <template #modal-footer="{ ok }">
                            <b-button v-if="modalGymClass.user.has_reservation_record === false || (modalGymClass.user.has_reservation_record === true && modalGymClass.user.has_active_reservation === false)"
                                      v-b-modal.modal-make-reservation size="sm"
                                      class="btn btn-primary"
                                      :class="[{'button-color-wave':modalGymClass.user.declined === false}, {'danger-button-color-wave':modalGymClass.user.declined === true}]"
                                      @click="createReservation" :disabled="modalGymClass.user.declined === true">
                                <span v-if="modalGymClass.user.declined === false">
                                    Book this class
                                </span>
                                <span v-else>
                                    Declined
                                </span>
                            </b-button>
                            <b-button v-if="modalGymClass.user.has_active_reservation === true"
                                      v-b-modal.modal-make-reservation class="btn btn-primary danger-button-color-wave" size="sm"
                                      @click="cancelReservation(modalGymClass.user.reservation_id)">
                                Cancel class
                            </b-button>

                            <b-button class="btn btn-primary button-color-wave" size="sm" @click="ok()">
                                OK
                            </b-button>
                        </template>
                    </b-modal>

                </div>

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
                currentMonth: null,
                currentYear: null,
                modalTitle: null,
                modalGymClass: [],
                form: {
                    user_id: this.auth.user.id,
                    gym_class_id: null,
                    week_day_id: null,
                    date: null,
                },
                isLoading: true,
            }
        },
        mounted() {
            this.buildCalendar();
        },
        methods: {
            buildCalendar() {
                let today = new Date();

                this.currentMonth = today.toLocaleString('default', { month: 'long' });
                // this.currentMonth = today.toLocaleString('el-GR', { month: 'long' });
                this.currentYear = today.getFullYear();

                axios.get('/calendar', this.form)
                    .then((results) => {
                        let count = 0;
                        this.calendarDates = [];
                        this.changeLineCalendarDateIndexes = [];

                        for (const [key, value] of Object.entries(results.data)) {
                            this.calendarDates.push(value);

                            if ((count+1)%7 === 0) {
                                this.changeLineCalendarDateIndexes.push(count);
                            }

                            ++count;
                        }

                        // loader
                        this.isLoading = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                    this.changeLineCalendarDateIndexes.forEach((value, index) => {
                        let d1 = document.getElementById('calendar_date'+value);
                        d1.insertAdjacentHTML('afterend', '<div class="w-100"></div>');
                    });
                });
            },
            buildModal(calendarDate, gymClass) {
                this.modalTitle = calendarDate.date_number + ' ' + calendarDate.month_name + ' - ' +  calendarDate.day_name;
                this.modalGymClass = gymClass;
                this.form.gym_class_id = gymClass.gym_class_id;
                this.form.week_day_id = gymClass.week_day_id;
                this.form.date = calendarDate.date + ' ' + gymClass.start_time;
            },
            createReservation() {
                // create reservation
                axios.post('/reservations', this.form)
                    .then((result) => {
                        // Hide the modals manually
                        this.$nextTick(() => {
                            this.$bvModal.hide('modal-student-calendar');
                        });

                        // display success message
                        this.$alertHandler.showAlert('Booking completed successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                    this.buildCalendar();
                });
            },
            cancelReservation(reservationId) {
                axios.post('/reservations/' + reservationId + '/cancel')
                    .then((result) => {
                        // Hide the modals manually
                        this.$nextTick(() => {
                            this.$bvModal.hide('modal-student-calendar');
                        });

                        // display success message
                        this.$alertHandler.showAlert('Booking canceled successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                    this.buildCalendar();
                });
            },
        }
    }
</script>
