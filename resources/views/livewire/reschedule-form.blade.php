<div>
    <!-- Loading Message -->
    <div wire:loading class="flex items-center space-x-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md p-2 mb-4">
        <!-- Spinner Icon -->
        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 1 1 16 0A8 8 0 0 1 4 12zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </svg>
        <!-- Loading Text -->
        <span class="text-sm font-medium">Processing...</span>
    </div>
    <!-- Card Blog -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-10 bg-white border my-2  shadow-md">
        <!-- Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 md:gap-12">
            <div class="text-center">
                <livewire:profile-image :user_id="$doctor_details->doctorUser->id" />
                <div class="mt-2 sm:mt-4">
                    <h3 class="text-sm font-medium text-gray-800 sm:text-base lg:text-lg dark:text-neutral-200">
                        {{$doctor_details->doctorUser->name}}
                    </h3>
                    <p class="text-xs text-gray-600 sm:text-sm lg:text-base dark:text-neutral-400">
                        {{$doctor_details->speciality->speciality_name}} / {{$doctor_details->hospital_name}}
                    </p>
                </div>
            </div>
            <!-- End Col -->
            <div class="text-center">
                <label for="">Select Appointment Type</label>
                <select wire:model="appointment_type" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                    <option selected="">Open this select menu</option>
                    <option value="0">On site</option>
                    <option value="1">Live consultation</option>
                </select>
                <h3>Select an Available Date</h3>
                <input type="text" id="datepicker" autocomplete="off" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none bg-gray-100 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Select Available date">
                @if($selectedDate)
                <div>
                    <h4>Selected Date: {{ $selectedDate }}</h4>
                </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold mb-2">Available Time Slots</h2>
                    <div class="flex flex-wrap">
                        @foreach ($timeSlots as $slot)
                        <button class="m-2 p-2 bg-blue-500 text-white rounded hover:bg-blue-700"
                            type="button"
                            wire:click="bookAppointment('{{$slot}}')"
                            wire:confirm="Do you really want to book an appointment on {{ $selectedDate }}, {{ $slot }} ?">
                            {{ date('H:i',strtotime($slot)) }} </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->
    </div>
    <!-- End Card Blog -->
    <script src="pikaday.js"></script>
    <script>
        // Inject available dates from Livewire
        var availableDates = @json($availableDates);

        var picker = new Pikaday({
            field: document.getElementById('datepicker'),
            format: 'YYYY-MM-DD',
            onSelect: function(date) {
                var selectedDate = picker.toString();
                @this.call('selectDate', selectedDate);
            },
            disableDayFn: function(date) {
                // Disable all dates not in the availableDates array
                var formattedDate = moment(date).format('YYYY-MM-DD');
                return !availableDates.includes(formattedDate);
            }
        });
    </script>

</div>