<div x-data="customDatePicker('{{ $label }}', '{{ $minDate }}')" class="relative w-80 mx-auto">
    <!-- Input Field -->
    <div class="border rounded-lg p-3 ">
        <label class="block text-xs text-gray-600 mb-1 font-medium" x-text="label"></label>
        <div class="absolute left-3 transform mr-2 pr-[15%] top-1/2 space-x-3">
            <!-- Animated SVG Icon -->
          <img src="/icons/date.svg" alt="from" class="size-7"/>
           </div>
        <input type="text" 
               x-model="formattedDate" 
               @click="showPicker = !showPicker"
               readonly
               placeholder="Select Date"
               class="w-full pl-[20%] text-lg font-semibold bg-white rounded-md px-2  focus:outline-none cursor-pointer">
    </div>

    <!-- Date Picker -->
    <div x-show="showPicker" @click.away="showPicker = false" 
         class="absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
        <!-- Header -->
        <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
            <h2 x-text="'Select a date for ' + label" class="text-sm font-semibold"></h2>
        </div>
        
        <div class="p-4">
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button @click.prevent="prevMonth()" type="button" class=" hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md">&lt;</button>
                <div x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()" class="font-semibold text-lg text-gray-700"></div>
                <button @click.prevent="nextMonth()" type="button" class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md">&gt;</button>
            </div>

            <!-- Days of the Week -->
            <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                <template x-for="day in days" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-7 gap-1">
                <template x-for="(date, index) in calendarDates" :key="index">
                    <div>
                        <div 
                            @click="date && selectDate(date)"
                            :class="{
                                'text-gray-300': !date || isPastDate(date),
                                'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date),
                                'bg-primary text-white': date && isSelected(date),
                                'border border-primary animate-bounce text-primary': date && isToday(date),
                                'rounded-full flex items-center justify-center h-8 w-8': true
                            }"
                            x-text="date ? date.getDate() : ''">
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function customDatePicker(label, minDate) {
    return {
        showPicker: false,
        formattedDate: '',
        selectedDate: null,
        currentMonth: new Date(),
        days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        calendarDates: [],
        minDate: minDate ? new Date(minDate) : null,
        label: label,

        init() {
            this.$watch('currentMonth', () => {
                this.generateCalendar();
            });
            this.generateCalendar();
        },

        formatDate(date) {
            const day = this.days[date.getDay()];
            const month = this.months[date.getMonth()].slice(0, 3);
            return `${day}, ${date.getDate()} ${month} ${date.getFullYear()}`;
        },

        isSelected(date) {
            return this.selectedDate && 
                date.getDate() === this.selectedDate.getDate() &&
                date.getMonth() === this.selectedDate.getMonth() &&
                date.getFullYear() === this.selectedDate.getFullYear();
        },

        isToday(date) {
            const today = new Date();
            return date &&
                date.getDate() === today.getDate() &&
                date.getMonth() === today.getMonth() &&
                date.getFullYear() === today.getFullYear();
        },

        isPastDate(date) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return this.minDate ? date < this.minDate : date < today;
        },

        selectDate(date) {
            if (!date || this.isPastDate(date)) return;
            this.selectedDate = date;
            this.formattedDate = this.formatDate(date);
            this.showPicker = false;
            
            // Format the date to YYYY-MM-DD before sending to Livewire
            const formattedDate = date.toISOString().split('T')[0];
            
            // Dispatch the date to the parent Livewire component
            this.$wire.call('dateSelected', formattedDate);
        },

        prevMonth() {
            const newDate = new Date(this.currentMonth);
            newDate.setMonth(newDate.getMonth() - 1);
            this.currentMonth = newDate;
        },

        nextMonth() {
            const newDate = new Date(this.currentMonth);
            newDate.setMonth(newDate.getMonth() + 1);
            this.currentMonth = newDate;
        },

        generateCalendar() {
            const year = this.currentMonth.getFullYear();
            const month = this.currentMonth.getMonth();
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const dates = [];
            // Add blank spaces for alignment
            for (let i = 0; i < firstDay; i++) dates.push(null);
            // Add actual days
            for (let day = 1; day <= daysInMonth; day++) {
                dates.push(new Date(year, month, day));
            }

            this.calendarDates = dates;
        }
    };
}
</script>