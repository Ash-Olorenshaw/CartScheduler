import { isAfter, isBefore } from 'date-fns'
import formatISO from 'date-fns/formatISO'
import { cloneDeep } from 'lodash'
import { computed, getCurrentInstance, onMounted, ref } from 'vue'

export default function useLocationFilter () {
    const instance = getCurrentInstance()
    const props = instance.props
    /**
     * @param {Date} date
     */
    const date = ref(new Date())
    const month = ref()

    const serverLocations = ref([])

    const getShifts = async () => {
        const response = await axios.get('/shifts')
        serverLocations.value = response.data.data
    }

    onMounted(() => {
        getShifts()
    })

    const selectedDate = computed({
        get () {
            return date.value ? formatISO(date.value, { representation: 'date' }) : ''
        },
        set (value) {
            date.value = value
        },
    })

    const foundVolunteer = (volunteers) => {
        for (const volunteer of volunteers) {
            if (volunteer.shift_date === selectedDate.value) {
                return true
            }
        }
        return false
    }

    const addShift = (shifts, shift) => {
        if (shift.available_from) {
            const from = new Date(shift.available_from)
            if (isBefore(date.value, from)) {
                return
            }
        }
        if (shift.available_to) {
            const to = new Date(shift.available_to)
            if (isAfter(date.value, to)) {
                return
            }
        }
        shifts.push(shift)
    }

    const addLocation = (mappedLocations, location, shift) => {
        const alreadyAddedLocation = mappedLocations.find(l => l.id === location.id)
        if (!alreadyAddedLocation) {
            location.filterShifts = []
            addShift(location.filterShifts, shift)
            mappedLocations.push(location)
        } else {
            if (!alreadyAddedLocation.filterShifts.find(s => s.id === shift.id)) {
                addShift(alreadyAddedLocation.filterShifts, shift)
            }
        }

    }

    const locations = computed(() => {
        if (!serverLocations?.value) {
            return
        }
        const mappedLocations = []
        const myLocations = cloneDeep(serverLocations.value)
        for (const location of myLocations) {
            for (const shift of location.shifts) {
                const volunteers = shift.volunteers
                if (foundVolunteer(volunteers)) {
                    shift.filterVolunteers = volunteers.filter(volunteer => volunteer.shift_date === selectedDate.value)
                }
                const dayOfWeek = date.value.getDay()
                const mappedDay = shift.js_days[dayOfWeek]
                if (mappedDay === true) {
                    addLocation(mappedLocations, location, shift)
                }
            }
        }
        return mappedLocations
    })

    return {
        date,
        locations,
    }
}