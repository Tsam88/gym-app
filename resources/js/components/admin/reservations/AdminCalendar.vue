<template>
    <div class="container-fluid calendar">
        <header>
            <h4 class="display-6 mb-4 text-center">{{currentMonth}} {{currentYear}}</h4>
            <div class="row d-none d-sm-flex p-1 text-white calendar-header">
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
                <div v-for="(calendarDate, index) in calendarDates" :id="'calendar_date'+index" class="day col-sm p-2 border border-left-0 border-top-0 text-truncate ">
                    <h5 class="row align-items-center">
                        <span class="date col-1">{{calendarDate.date_number}} {{calendarDate.month_name}}</span>
                        <small class="col d-sm-none text-center text-muted">{{calendarDate.day_name}}</small>
                        <span class="col-1"></span>
                    </h5>
                    <a v-for="gym_class in calendarDate.gym_classes" class="event d-block p-1 pl-2 pr-2 mb-1 rounded text-truncate small bg-success text-white" title="Test Event 2">{{gym_class.start_time}} {{gym_class.gym_class_name}}</a>
<!--                    <a class="event d-block p-1 pl-2 pr-2 mb-1 rounded text-truncate small bg-danger text-white" title="Test Event 3">Test Event 3</a>-->
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
