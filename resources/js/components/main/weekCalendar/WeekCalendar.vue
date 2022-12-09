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
                                    <tr v-for="(weekDays, index1) in selectedWeekDaysPerTime">
                                        <td class="class-time" :class="{'no-border-bottom':index1 === Object.keys(selectedWeekDaysPerTime).length-1}">{{weekDays.start_time}}</td>
                                        <td v-for="(gymClasses, index2) in weekDays.days" :class="[{'dark-bg':(index1%2 === 0 && index2%2 === 0) || (index1%2 !== 0 && index2%2 !== 0)}]">
                                            <div class="class-height" :class="{'hover-bg': gymClasses.length === 1}">
                                                <div v-for="gymClass in gymClasses" :class="{'hover-bg': gymClasses.length > 1}">
<!--                                                    <h5 :style="{color: gymClass.color}">{{gymClass.gym_class_name}}</h5>-->
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
                selectedWeekDaysPerTime: [],
                gymClassNames: [],
                selectedClass: 'all_classes',
            }
        },
        mounted() {
            axios.get('/calendar/week', this.form)
                .then((results) => {
                    this.allWeekDaysPerTime = results.data['week_calendar'];
                    this.selectedWeekDaysPerTime = results.data['week_calendar'];
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

                // if all classes are selected, then set selectedWeekDaysPerTime equal to allWeekDaysPerTime
                if (className === 'all_classes') {
                    this.selectedWeekDaysPerTime = this.allWeekDaysPerTime;
                    return;
                }

                // empty selectedWeekDaysPerTime variable so we can build it based on the selected class
                this.selectedWeekDaysPerTime = [];
                let weekDaysPerTime = {};

                // for each datetime object (each row in weekly calendar)
                this.allWeekDaysPerTime.forEach((weekDays, index) => {
                    // empty weekDaysPerTime variable for every datetime object (every row in weekly calendar)
                    weekDaysPerTime = {};

                    // for each day of the week
                    weekDays.days.forEach((gymClasses, index2) => {

                        // for each class in day
                        gymClasses.forEach((gymClass) => {
                            // check if the class is equal to the selected class
                            if (gymClass.gym_class_name === className) {
                                // if weekDaysPerTime is empty, then initialize it
                                if (Object.keys(weekDaysPerTime).length === 0) {
                                    weekDaysPerTime = {
                                        'days': [],
                                        'start_time': weekDays.start_time
                                    };

                                    // push 7 empty arrays (as the days of the week) to days property
                                    for (let i = 0; i < 7; i++) {
                                        weekDaysPerTime.days.push([]);
                                    }
                                }

                                // push gym class
                                weekDaysPerTime.days[index2].push(gymClass);
                            }
                        });

                    });

                    // if there classes based on the selected class, then add them to the weekly calendar
                    if (Object.keys(weekDaysPerTime).length !== 0) {
                        this.selectedWeekDaysPerTime.push(weekDaysPerTime);
                    }

                });
            },
        }
    }
</script>
