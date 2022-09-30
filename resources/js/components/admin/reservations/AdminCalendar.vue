<template>
    <div class="container-fluid calendar">
        <header>
            <h4 class="display-6 mb-4 text-center">{{currentMonth}} {{currentYear}}</h4>
            <div class="row d-none d-lg-flex p-1 text-white calendar-header">
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
        <div v-if="!isLoading" class="row border border-right-0 border-bottom-0">
            <div v-for="(calendarDate, index) in calendarDates" :id="'calendar_date'+index" class="day col-lg p-2 border border-left-0 border-top-0 text-truncate" :class="[{'d-none d-sm-inline-block bg-light text-muted':calendarDate.disabled === true}, {'hide-date':calendarDate.gym_classes.length === 0}]">
                <h5 class="row align-items-center">
                    <b class="date col-1 text-muted">{{calendarDate.date_number}} {{calendarDate.month_name}}</b>
                    <b class="col d-lg-none text-center text-muted">{{calendarDate.day_name}}</b>
                    <span class="col-1"></span>
                </h5>
                <a v-for="gym_class in calendarDate.gym_classes" @click="buildModal(calendarDate, gym_class)" v-b-modal.modal-admin-calendar class="event d-block p-1 pl-2 pr-2 mb-2 mb-lg-1 rounded text-truncate small bg-success text-white" title="Test Event 2">{{gym_class.start_time}} {{gym_class.gym_class_name}}</a>
            </div>
        </div>

        <div class="container mt-5">

            <!--Show reservations modal-->
            <b-modal id="modal-admin-calendar">
                <template #modal-header="{ close }">
                    <b>{{modalGymClass.gym_class_name}}</b>
                    <!-- Emulate built in modal header close button action -->
                    <span class="modal-header-close-button" @click="close()">×</span>
                </template>

                <div class="mb-4">
                    <div class="modal-info text-muted mb-2">
                        <b>
                            <i>{{modalTitle}} | {{modalGymClass.start_time}} - {{modalGymClass.end_time}}</i>
                            <br>
                            <i v-if="modalGymClass.teacher">{{modalGymClass.teacher}}</i>
                        </b>
                    </div>

                    <p>{{modalGymClass.description}}</p>

                    <hr>

                    <u><b>Students ({{modalGymClass.number_of_reservations}}/{{modalGymClass.number_of_students_limit}}):</b></u>
                    <div v-if="modalGymClass.users && modalGymClass.users.length > 0">
                        <ul class="list-group list-group-flush modal-reservation-list">
                            <li v-for="(user, modalGymClassUsersIndex) in modalGymClass.users" class="list-group-item">
                                <span>{{user.user_name}} {{user.user_surname}}</span>
                                <span class="float-right">
                                    <b-button v-if="user.declined === true" @click="acceptReservation(user.user_id, modalGymClassUsersIndex)" class="button-color-wave button-small-size">Book</b-button>
                                    <b-button v-if="user.declined === false" @click="declineReservation(user.reservation_id, modalGymClassUsersIndex)" class="danger-button-color-wave button-small-size pl-6">Decline</b-button>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <template #modal-footer="{ ok }">
                    <b-button v-b-modal.modal-make-reservation class="btn btn-primary button-color-wave" size="sm" @click="getUsers">
                        Book this class
                    </b-button>

                    <b-button class="btn btn-primary button-color-wave" size="sm" @click="ok()">
                        OK
                    </b-button>
                </template>
            </b-modal>

            <!--Make reservation for student modal-->
            <b-modal id="modal-make-reservation" size="sm" @ok="handleOk" @close="handleClose" @cancel="handleClose">
                <template #modal-header="{ close }">
                    <b>Book a class for student</b>
                    <!-- Emulate built in modal header close button action -->
                    <span class="modal-header-close-button" @click="close()">×</span>
                </template>

                <form ref="form" @submit.stop.prevent="submitForm">
                    <b-form-group label="Student" label-for="user_id">
                        <b-form-select v-model="form.user_id" id="user_id" name="user_id" class="form-select mb-3" :state="state.user_id" required>
                            <option value=null selected>Select student</option>
                            <option v-for="user in usersList" :value="user.id">{{user.name}} {{user.surname}}</option>
                        </b-form-select>
                        <b-form-invalid-feedback id="user_id">
                            {{validationMessages.user_id}}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </form>

                <template #modal-footer="{ cancel }">
                    <b-button class="btn btn-primary button-color-wave" size="sm" @click="submitForm">
                        Book this class
                    </b-button>

                    <b-button class="btn btn-primary button-color-wave" size="sm" @click="cancel()">
                        Cancel
                    </b-button>
                </template>
            </b-modal>

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
                displayModal: false,
                modalTitle: null,
                modalGymClass: [],
                usersList: [],
                state: {
                    user_id: null,
                },
                validationMessages: {
                    user_id: 'Student is required',
                },
                form: {
                    user_id: null,
                    gym_class_id: null,
                    week_day_id: null,
                    date: null,
                    // isPastDate -> fix it in reservation service
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
            getUsers() {
                axios.get('/admin/users')
                    .then((results) => {
                        results.data.data.forEach((value, index) => {
                            this.usersList.push(value);
                        });
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                });
            },
            createReservation(modalGymClassUsersIndex = null) {
                // create reservation
                axios.post('/admin/reservations', this.form)
                    .then((result) => {
                        // clear form values
                        this.form.user_id = null;
                        // clear state values
                        this.state.user_id = null;

                        // check if the "create reservation" action is coming from modal-admin-calendar or modal-make-reservation
                        // modalGymClassUsersIndex is not null the the action is coming from modal-admin-calendar
                        if (modalGymClassUsersIndex !== null) {
                            this.modalGymClass.users[modalGymClassUsersIndex].declined = false;
                            ++this.modalGymClass.number_of_reservations;
                        } else {
                            // Hide the modals manually
                            this.$nextTick(() => {
                                this.$bvModal.hide('modal-make-reservation');
                                this.$bvModal.hide('modal-admin-calendar');
                            });
                        }

                        // display success message
                        this.$alertHandler.showAlert('Booking for student completed successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                    this.buildCalendar();
                });
            },
            submitForm() {
                // validate form
                if (!this.checkFormValidity()) {
                    return;
                }

                this.createReservation();
            },
            acceptReservation(userId, modalGymClassUsersIndex) {
                this.form.user_id = userId;
                this.createReservation(modalGymClassUsersIndex);
            },
            declineReservation(reservationId, modalGymClassUsersIndex) {
                axios.post('/admin/reservations/' + reservationId + '/decline')
                    .then((result) => {
                        this.modalGymClass.users[modalGymClassUsersIndex].declined = true;
                        --this.modalGymClass.number_of_reservations;

                        // display success message
                        this.$alertHandler.showAlert('Booking for student declined successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                    this.buildCalendar();
                });
            },
            handleOk(bvModalEvent) {
                // Prevent modal from closing
                bvModalEvent.preventDefault();
                // Trigger submit handler
                this.submitForm()
            },
            handleClose() {
                this.form.user_id = null;
                this.state.user_id = null;
            },
            checkFormValidity() {
                const valid = this.$refs.form.checkValidity() && this.form.user_id > 0;
                this.state.user_id = valid;
                this.validationMessages.user_id = 'Student is required';
                return valid;
            },
        }
    }
</script>
