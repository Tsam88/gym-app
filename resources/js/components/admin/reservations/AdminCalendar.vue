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
            // updateGymClass(gymClassId) {
            //     this.$router.push({ name: 'UpdateGymClasses', params: { id: gymClassId } })
            // }
        }
    }
</script>
