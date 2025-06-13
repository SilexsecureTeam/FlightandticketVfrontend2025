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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <div>
            <div class="text-white text-xs lg:text-base" x-text="label"></div>
            <input 
                type="text" 
                :name="name" 
                placeholder="{{ $placeholder }}" 
                @click="pickerType === 'departure' ? showDeparturePicker = !showDeparturePicker : showReturnPicker = !showReturnPicker" 
                readonly 
                x-model="pickerType === 'departure' ? departureDateFormatted : returnDateFormatted" 
                class="w-full placeholder:text-xs bg-transparent text-white text-xs lg:text-xl focus:outline-none cursor-pointer"
            >
            <input 
                type="hidden" 
                :name="`${name}_hidden`" 
                x-model="pickerType === 'departure' ? (selectedDepartureDate ? formatDateValue(selectedDepartureDate) : '') : (selectedReturnDate ? formatDateValue(selectedReturnDate) : '')"
            >
        </div>
    </div>

    <!-- Departure Date Picker -->
    <div 
    x-cloak
        x-show="pickerType === 'departure' && showDeparturePicker" 
        @click.away="showDeparturePicker = false" 
        class="absolute top-0 left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full"
    >
        <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
            <h2 x-text="`Select ${label}`" class="text-xs font-semibold"></h2>
        </div>
        <div class="p-4">
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button 
                    @click.prevent="prevMonth('departure')" 
                    type="button" 
                    class="hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md"
                >&lt;</button>
                <div 
                    x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()" 
                    class="font-semibold text-lg text-gray-700"
                ></div>
                <button 
                    @click.prevent="nextMonth('departure')" 
                    type="button" 
                    class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md"
                >&gt;</button>
            </div>
            <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                <template x-for="day in days" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1 text-xs">
                <template x-for="(date, index) in departureCalendarDates" :key="index">
                    <div>
                        <div 
                            @click="selectDate(date, 'departure')"
                            :class="{
                                'text-gray-300': !date || isPastDate(date, 'departure'),
                                'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date, 'departure'),
                                'bg-primary text-white': date && isSelected(date, 'departure'),
                                'border border-primary animate-bounce text-primary': date && isToday(date),
                                'rounded-full flex items-center justify-center h-8 w-8': true
                            }"
                            x-text="date ? date.getDate() : ''"
                        ></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Return Date Picker -->
    <div 
    x-cloak
        x-show="pickerType === 'return' && showReturnPicker" 
        @click.away="showReturnPicker = false" 
        class="absolute top-0 left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full"
    >
        <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
            <h2 x-text="`Select ${label}`" class="text-xs font-semibold"></h2>
        </div>
        <div class="p-4">
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button 
                    @click.prevent="prevMonth('return')" 
                    type="button" 
                    class="hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md"
                >&lt;</button>
                <div 
                    x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()" 
                    class="font-semibold text-lg text-gray-700"
                ></div>
                <button 
                    @click.prevent="nextMonth('return')" 
                    type="button" 
                    class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md"
                >&gt;</button>
            </div>
            <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                <template x-for="day in days" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1 text-xs">
                <template x-for="(date, index) in returnCalendarDates" :key="index">
                    <div>
                        <div 
                            @click="selectDate(date, 'return')"
                            :class="{
                                'text-gray-300': !date || isPastDate(date, 'return'),
                                'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date, 'return'),
                                'bg-primary text-white': date && isSelected(date, 'return'),
                                'border border-primary animate-bounce text-primary': date && isToday(date),
                                'rounded-full flex items-center justify-center h-8 w-8': true
                            }"
                            x-text="date ? date.getDate() : ''"
                        ></div>
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
                
                init() {
                    if (initialDate) {
                        const date = new Date(initialDate);
                        if (this.pickerType === 'departure') {
                            this.selectedDepartureDate = date;
                            this.departureDateFormatted = this.formatDate(date);
                        } else {
                            this.selectedReturnDate = date;
                            this.returnDateFormatted = this.formatDate(date);
                        }
                    }
                    
                    this.$watch('currentMonth', () => {
                        this.generateCalendar('departure');
                        this.generateCalendar('return');
                    });
                    
                    this.generateCalendar('departure');
                    this.generateCalendar('return');
                },
                
                generateCalendar(type) {
                    const year = this.currentMonth.getFullYear();
                    const month = this.currentMonth.getMonth();
                    const firstDay = new Date(year, month, 1).getDay(); // First day of the month
                    const daysInMonth = new Date(year, month + 1, 0).getDate(); // Total days in the month
                    
                    const dates = [];
                    for (let i = 0; i < firstDay; i++) dates.push(null); // Padding for previous month's days
                    for (let day = 1; day <= daysInMonth; day++) {
                        dates.push(new Date(year, month, day)); // Add current month's days
                    }
                    
                    if (type === 'departure') {
                        this.departureCalendarDates = dates;
                    } else {
                        this.returnCalendarDates = dates;
                    }
                },
                
                formatDate(date) {
                    const day = this.days[date.getDay()];
                    const month = this.months[date.getMonth()].slice(0, 3);
                    return `${day}, ${date.getDate()} ${month} ${date.getFullYear()}`;
                },
                
                formatDateValue(date) {
                    // Format as YYYY-MM-DD for form submission
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },
                
                isSelected(date, type) {
                    const selectedDate = type === 'departure' ? this.selectedDepartureDate : this.selectedReturnDate;
                    return (
                        selectedDate &&
                        date.getDate() === selectedDate.getDate() &&
                        date.getMonth() === selectedDate.getMonth() &&
                        date.getFullYear() === selectedDate.getFullYear()
                    );
                },
                
                isToday(date) {
                    if (!date) return false;
                    const today = new Date();
                    return (
                        date.getDate() === today.getDate() &&
                        date.getMonth() === today.getMonth() &&
                        date.getFullYear() === today.getFullYear()
                    );
                },
                
                isPastDate(date, type) {
                    if (!date) return false;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Reset time to midnight
                    
                    if (this.minDate && type === 'return') {
                        return date < today || date < this.minDate;
                    }
                    
                    return date < today;
                },
                
                selectDate(date, type) {
                    if (!date || this.isPastDate(date, type)) return;
                    
                    // Ensure the date is not modified unintentionally
                    const selectedDate = new Date(date.getTime());
                    
                    if (type === 'departure') {
                        this.selectedDepartureDate = selectedDate;
                        this.departureDateFormatted = this.formatDate(selectedDate);
                        this.showDeparturePicker = false;
                    } else {
                        this.selectedReturnDate = selectedDate;
                        this.returnDateFormatted = this.formatDate(selectedDate);
                        this.showReturnPicker = false;
                    }
                    
                    // If Livewire integration is enabled
                    if (this.wire) {
                        // Format date for Livewire as MM/DD/YYYY
                        const formattedForLivewire = selectedDate.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: '2-digit', 
                            day: '2-digit' 
                        });
                        
                        // Call Livewire method
                        this.$wire.call('dateSelected', formattedForLivewire, type);
                    }
                },
                
                prevMonth(type) {
                    const newDate = new Date(this.currentMonth);
                    newDate.setMonth(newDate.getMonth() - 1);
                    this.currentMonth = newDate;
                },
                
                nextMonth(type) {
                    const newDate = new Date(this.currentMonth);
                    newDate.setMonth(newDate.getMonth() + 1);
                    this.currentMonth = newDate;
                }
            };
        };
    }
</script>