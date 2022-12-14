<template>
    <div>
        <!-- Class Timetable Section Begin -->
        <section id="weekly-program" class="class-timetable-section spad">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <span>Weekly Program</span>
                            <h2>FIND YOUR TIME</h2>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="table-controls mb-2">
                            <ul class="px-1 py-1">
                                <li @click="selectClass('all_classes')" class="pb-1" :class="{'active':selectedClass === 'all_classes'}">All classes</li>
                                <li v-for="gymClassName in gymClassNames" @click="selectClass(gymClassName)" :class="{'active':selectedClass === gymClassName}">{{gymClassName}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="class-timetable-overflow-x flipped">
                            <div class="class-timetable">
                                <table>
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Monday</th>
                                        <th>Tuesday</th>
                                        <th>Wednesday</th>
                                        <th>Thursday</th>
                                        <th>Friday</th>
                                        <th>Saturday</th>
                                        <th class="no-border-right">Sunday</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-show="Object.keys(selectedWeekDaysPerTime).includes(weekDays.start_time)" v-for="(weekDays, index1) in allWeekDaysPerTime">
                                        <td class="class-time" :class="{'no-border-bottom':index1 === Object.keys(allWeekDaysPerTime).length-1}">{{weekDays.start_time}}</td>
                                        <td v-for="gymClasses in weekDays.days" :class="{'dark-bg':setDarkBackground() === true}">
                                            <div class="class-height" :class="{'hover-bg': gymClasses.length === 1}">
                                                <div v-show="Object.keys(selectedWeekDaysPerTime).includes(weekDays.start_time) && selectedWeekDaysPerTime[weekDays.start_time].includes(gymClass.week_day_id)"
                                                     v-for="gymClass in gymClasses" class="fade-class" :class="[{'hover-bg': gymClasses.length > 1}]">
                                                    <h5>{{gymClass.gym_class_name}}</h5>
                                                    <span>{{gymClass.teacher}}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Class Timetable Section End -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                allWeekDaysPerTime: {},
                selectedWeekDaysPerTime: {},
                gymClassNames: [],
                selectedClass: 'all_classes',
                darkBackground: false,
            }
        },
        created() {
            axios.get('/calendar/week', this.form)
                .then((results) => {
                    this.allWeekDaysPerTime = results.data['week_calendar'];
                    this.selectClass(this.selectedClass);
                    this.gymClassNames = results.data['gym_class_names'];
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods: {
            selectClass(className) {
                this.selectedClass = className;
                this.selectedWeekDaysPerTime = {};

                // for each datetime object (each row in weekly calendar)
                this.allWeekDaysPerTime.forEach((weekDays, index) => {
                    // for each day of the week
                    weekDays.days.forEach((gymClasses, index2) => {
                        // for each class in day
                        gymClasses.forEach((gymClass) => {
                            // check if the class is equal to the selected class
                            if (gymClass.gym_class_name === className || className === 'all_classes') {
                                if (!this.selectedWeekDaysPerTime[weekDays.start_time]) {
                                    this.selectedWeekDaysPerTime[weekDays.start_time] = [];
                                }

                                this.selectedWeekDaysPerTime[weekDays.start_time].push(gymClass.week_day_id);
                            }
                        });
                    });
                });
            },
            setDarkBackground() {
                this.darkBackground = !this.darkBackground;

                return this.darkBackground;
            },
        }
    }
</script>
