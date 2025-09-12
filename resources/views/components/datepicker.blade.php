@props([
'label' => 'Date',
'name' => 'date',
'placeholder' => 'Select a date',
'initialDate' => null,
'minDate' => null,
'type' => 'departure',
'wire' => null,
])

<div x-data="datePickerComponent('{{ $minDate }}', '{{ $label }}', '{{ $type }}', '{{ $initialDate }}', '{{ $wire }}')" class="relative w-full">
    <div class="relative flex rounded-lg gap-3 p-3 mb-4">
        <svg class="lg:size-16 size-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <div>
            <div class="text-white text-xs lg:text-base" x-text="label"></div>
            <input type="text" :name="name" placeholder="{{ $placeholder }}"
                @click="showCalendarNow(pickerType)"
                readonly x-model="pickerType === 'departure' ? departureDateFormatted : returnDateFormatted"
                class="w-full placeholder:text-xs bg-transparent text-white text-xs lg:text-xl focus:outline-none cursor-pointer">
            <input type="hidden" :name="`${name}_hidden`"
                x-model="pickerType === 'departure' ? (selectedDepartureDate ? formatDateValue(selectedDepartureDate) : '') : (selectedReturnDate ? formatDateValue(selectedReturnDate) : '')">
        </div>
    </div>

    <!-- Departure Date Picker -->
    <div x-cloak x-show="pickerType === 'departure' && showDeparturePicker" @click.away="showDeparturePicker = false"
        class="absolute top-0 left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
        <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
            <h2 x-text="`Select ${label}`" class="text-xs font-semibold"></h2>
        </div>
        <div class="p-4">
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button @click.prevent="prevMonth('departure')" type="button"
                    class="hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md">&lt;</button>
                <div x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()"
                    class="font-semibold text-lg text-gray-700"></div>
                <button @click.prevent="nextMonth('departure')" type="button"
                    class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md">&gt;</button>
            </div>
            <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                <template x-for="day in days" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1 text-xs">
                <template x-for="(date, index) in departureCalendarDates" :key="index">
                    <div>
                        <div @click="selectDate(date, 'departure')"
                            :class="{
                                'text-gray-300': !date || isPastDate(date, 'departure'),
                                'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date,
                                    'departure'),
                                'bg-primary text-white': date && isSelected(date, 'departure'),
                                'border border-primary animate-bounce text-primary': date && isToday(date),
                                'rounded-full flex items-center justify-center h-8 w-8': true
                            }"
                            x-text="date ? date.getDate() : ''"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Return Date Picker -->
    <div x-cloak x-show="pickerType === 'return' && showReturnPicker" @click.away="showReturnPicker = false"
        class="absolute top-0 left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
        <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
            <h2 x-text="`Select ${label}`" class="text-xs font-semibold"></h2>
        </div>
        <div class="p-4">
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button @click.prevent="prevMonth('return')" type="button"
                    class="hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md">&lt;</button>
                <div x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()"
                    class="font-semibold text-lg text-gray-700"></div>
                <button @click.prevent="nextMonth('return')" type="button"
                    class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md">&gt;</button>
            </div>
            <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                <template x-for="day in days" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1 text-xs">
                <template x-for="(date, index) in returnCalendarDates" :key="index">
                    <div>
                        <div @click="selectDate(date, 'return')"
                            :class="{
                                'text-gray-300': !date || isPastDate(date, 'return'),
                                'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date, 'return'),
                                'bg-primary text-white': date && isSelected(date, 'return'),
                                'border border-primary animate-bounce text-primary': date && isToday(date),
                                'rounded-full flex items-center justify-center h-8 w-8': true
                            }"
                            x-text="date ? date.getDate() : ''"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    // Only define the function if it doesn't already exist
    if (typeof window.datePickerComponent === 'undefined') {
        window.datePickerComponent = function(minDate, label, type = 'departure', initialDate = null, wire = null) {
            return {
                showDeparturePicker: false,
                showReturnPicker: false,
                departureDateFormatted: '',
                returnDateFormatted: '',
                selectedDepartureDate: null,
                selectedReturnDate: null,
                currentMonth: new Date(),
                days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                months: [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ],
                departureCalendarDates: [],
                returnCalendarDates: [],
                minDate: minDate ? new Date(minDate) : null,
                label: label || 'Date',
                pickerType: type || 'departure',
                wire: wire || null,
                name: 'date',
                init() {
                    // Set initial date if provided
                    if (initialDate && initialDate !== 'null') {
                        const date = new Date(initialDate);
                        if (!isNaN(date.getTime())) {
                            if (this.pickerType === 'departure') {
                                this.selectedDepartureDate = date;
                                this.departureDateFormatted = this.formatDate(date);
                            } else {
                                this.selectedReturnDate = date;
                                this.returnDateFormatted = this.formatDate(date);
                            }
                            this.currentMonth = new Date(date.getFullYear(), date.getMonth(), 1);
                        }
                    }

                    // Generate calendar dates
                    this.generateCalendarDates('departure');
                    this.generateCalendarDates('return');

                    // Set minimum date if not provided
                    if (!this.minDate) {
                        this.minDate = new Date();
                        this.minDate.setHours(0, 0, 0, 0);
                    }
                },

                formatDate(date) {
                    if (!date) return '';
                    return `${date.getDate()} ${this.months[date.getMonth()].substring(0, 3)} ${date.getFullYear()}`;
                },

                formatDateValue(date) {
                    if (!date) return '';
                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                    const day = date.getDate().toString().padStart(2, '0');
                    return `${date.getFullYear()}-${month}-${day}`;
                },

                generateCalendarDates(type) {
                    const year = this.currentMonth.getFullYear();
                    const month = this.currentMonth.getMonth();

                    // First day of the month
                    const firstDay = new Date(year, month, 1);
                    // Last day of the month
                    const lastDay = new Date(year, month + 1, 0);

                    // Calculate days from previous month to fill the first week
                    const daysFromPrevMonth = firstDay.getDay();

                    // Calculate total days needed (previous month days + current month days)
                    const totalDays = daysFromPrevMonth + lastDay.getDate();

                    // Calculate rows needed (ceil to ensure we have enough rows)
                    const rows = Math.ceil(totalDays / 7);

                    // Total cells in the calendar (7 days * rows)
                    const totalCells = rows * 7;

                    // Create array for calendar dates
                    const calendarDates = [];

                    // Add empty placeholders for days from previous month
                    for (let i = 0; i < daysFromPrevMonth; i++) {
                        calendarDates.push(null);
                    }

                    // Add days from current month only
                    for (let i = 1; i <= lastDay.getDate(); i++) {
                        calendarDates.push(new Date(year, month, i));
                    }

                    // Add empty placeholders for remaining cells
                    const remainingCells = totalCells - calendarDates.length;
                    for (let i = 0; i < remainingCells; i++) {
                        calendarDates.push(null);
                    }

                    // Update the appropriate calendar dates array
                    if (type === 'departure') {
                        this.departureCalendarDates = calendarDates;
                    } else {
                        this.returnCalendarDates = calendarDates;
                    }
                },

                prevMonth(type) {
                    this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() - 1,
                        1);
                    this.generateCalendarDates(type);
                },

                nextMonth(type) {
                    this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1,
                        1);
                    this.generateCalendarDates(type);
                },

                selectDate(date, type) {
                    if (!date || this.isPastDate(date, type)) return;

                    const selectedDate = new Date(date.getTime());
                    if (type === 'departure') {
                        this.selectedDepartureDate = date;
                        this.departureDateFormatted = this.formatDate(date);
                        this.showDeparturePicker = false;

                        // If return date is before departure date, clear it
                        if (this.selectedReturnDate && this.selectedReturnDate < date) {
                            this.selectedReturnDate = null;
                            this.returnDateFormatted = '';
                        }
                        sessionStorage.setItem('storeDepartureDate', date);
                    } else {
                        // Ensure return date is not before departure date
                        if (this.selectedDepartureDate && date < this.selectedDepartureDate) return;

                        this.selectedReturnDate = date;
                        this.returnDateFormatted = this.formatDate(date);
                        this.showReturnPicker = false;

                        sessionStorage.removeItem('storeDepartureDate')
                    }

                    // If Livewire integration is enabled
                    if (this.wire) {
                        // Format date for Livewire in ISO 8601 format (YYYY-MM-DD)
                        const year = selectedDate.getFullYear();
                        const month = (selectedDate.getMonth() + 1).toString().padStart(2, '0');
                        const day = selectedDate.getDate().toString().padStart(2, '0');
                        const formattedForLivewire = `${year}-${month}-${day}`;

                        // Call Livewire method
                        if (this.wire === 'true') {
                            this.$wire.call('dateSelected', formattedForLivewire, type);
                        } else {
                            // Extract index from name attribute (e.g., "flight_1_date" -> 1)
                            const index = this.name.split('_')[1];
                            this.$wire.call('multiCityDateSelected', formattedForLivewire, wire);
                        }
                    }
                },

                isPastDate(date, type) {
                    if (!date) return true;

                    // Create date objects with time set to midnight for accurate comparison
                    const compareDate = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0);
                    const today = sessionStorage.getItem("storeDepartureDate") ? new Date(sessionStorage.getItem("storeDepartureDate")) : new Date();
                    today.setHours(0, 0, 0, 0);

                    // For departure dates, check against minDate or today
                    if (type === 'departure') {
                        const minCompareDate = this.minDate ?
                            new Date(this.minDate.getFullYear(), this.minDate.getMonth(), this.minDate.getDate(), 0,
                                0, 0, 0) :
                            today;
                        return compareDate < minCompareDate;
                    }

                    // For return dates, check against selectedDepartureDate or minDate or today
                    if (this.selectedDepartureDate) {
                        const departureCompareDate = new Date(
                            this.selectedDepartureDate.getFullYear(),
                            this.selectedDepartureDate.getMonth(),
                            this.selectedDepartureDate.getDate(),
                            0, 0, 0, 0
                        );
                        return compareDate < today;
                    }

                    const minCompareDate = this.minDate ?
                        new Date(this.minDate.getFullYear(), this.minDate.getMonth(), this.minDate.getDate(), 0, 0,
                            0, 0) :
                        today;
                    return compareDate < today;
                },

                isSelected(date, type) {
                    if (!date) return false;

                    if (type === 'departure' && this.selectedDepartureDate) {
                        return date.getDate() === this.selectedDepartureDate.getDate() &&
                            date.getMonth() === this.selectedDepartureDate.getMonth() &&
                            date.getFullYear() === this.selectedDepartureDate.getFullYear();
                    }

                    if (type === 'return' && this.selectedReturnDate) {
                        return date.getDate() === this.selectedReturnDate.getDate() &&
                            date.getMonth() === this.selectedReturnDate.getMonth() &&
                            date.getFullYear() === this.selectedReturnDate.getFullYear();
                    }

                    return false;
                },

                isToday(date) {
                    if (!date) return false;

                    const today = sessionStorage.getItem("storeDepartureDate") ? new Date(sessionStorage.getItem("storeDepartureDate")) : new Date();
                    return date.getDate() === today.getDate() &&
                        date.getMonth() === today.getMonth() &&
                        date.getFullYear() === today.getFullYear();
                },
                showCalendarNow(pickerType) {
                    pickerType === 'departure' ? this.showDeparturePicker = !this.showDeparturePicker : this.showReturnPicker = !this.showReturnPicker

                    if (pickerType === 'return') {
                        this.currentMonth = sessionStorage.getItem("storeDepartureDate") ? new Date(sessionStorage.getItem("storeDepartureDate")) : new Date()
                        this.generateCalendarDates("return");
                    }
                }
            };
        };
    }
</script>