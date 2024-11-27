<template>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 col-sm-12">
          <h6>Information</h6>
          <div class="form-group">
            <label for="name">Name</label>
            <input id="name" type="text" class="form-control" v-model="form.name">
          </div>
          <div class="form-group">
            <label for="package_id">Package</label>
            <v-select id="package_id" v-model="form.package_id" :options="packages" :reduce="option => option.id"
                      label="title"></v-select>
          </div>
          <div class="form-group" v-if="!form.is_anytime">
            <label>Scheduled At</label>
            <datetime value-zone="UTC+07:00" input-class="form-control" v-model="form.scheduled_at"
                      type="datetime"></datetime>
          </div>
          <div class="form-group">
            <button class="btn btn-success btn-block" @click="save">Save</button>
          </div>
          <!--          <div class="form-group">-->
          <!--            <label>Anytime</label>-->
          <!--            <div class="selectgroup selectgroup-pills">-->
          <!--              <label class="selectgroup-item">-->
          <!--                <input type="radio" name="is_anytime_1" :value="true" class="selectgroup-input" v-model="form.is_anytime">-->
          <!--                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-check"></i></span>-->
          <!--              </label>-->
          <!--              <label class="selectgroup-item">-->
          <!--                <input type="radio" name="is_anytime_2" :value="false" class="selectgroup-input" v-model="form.is_anytime">-->
          <!--                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-times"></i></span>-->
          <!--              </label>-->
          <!--            </div>-->
          <!--          </div>-->
          <!--          <div class="form-group">-->
          <!--            <label>Duration</label>-->
          <!--            <input type="number" v-model="form.duration" placeholder="Duration in minutes" class="form-control">-->
          <!--          </div>-->
          <!--          <div class="form-group">-->
          <!--            <label>Multi Attempt</label>-->
          <!--            <div class="selectgroup selectgroup-pills">-->
          <!--              <label class="selectgroup-item">-->
          <!--                <input type="radio" name="is_multi_attempt_1" :value="true" class="selectgroup-input" v-model="form.is_multi_attempt">-->
          <!--                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-check"></i></span>-->
          <!--              </label>-->
          <!--              <label class="selectgroup-item">-->
          <!--                <input type="radio" name="is_multi_attempt_2" :value="false" class="selectgroup-input" v-model="form.is_multi_attempt">-->
          <!--                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-times"></i></span>-->
          <!--              </label>-->
          <!--            </div>-->
          <!--          </div>-->
          <!--          <div class="form-group">-->
          <!--            <label for="participants">Participants</label>-->
          <!--            <v-select id="participants" v-model="form.participants" multiple :options="parsingUsers" :reduce="option => option.hash" label="name"></v-select>-->
          <!--          </div>-->
        </div>
        <div class="col-md-6 col-sm-12">
          <h6>Participants</h6>
          <div class="row">
            <div class="col-md-6 col-sm-12" v-for="(participant, key) in selectedParticipants"
                 :key="`selected-participant-${key}`">
              <div class="card">
                <div class="card-body" style="display: flex; justify-content: space-between">
                  <p><small class="text-info">{{
                      participant.alt_id ? participant.alt_id + ' - ' : ''
                    }}</small>{{ participant.name }}</p>
                  <button class="btn btn-danger btn-sm" @click="retractParticipant(participant)"><i
                    class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
          </div>
          <input id="search" type="text" class="form-control" placeholder="type to search..."
                 v-model="participantQuery">
          <div class="table-responsive">
            <table class="table table-striped table-sm table-md">
              <thead>
              <tr>
                <th>#</th>
                <th>ID - Name</th>
                <th>Username</th>
                <th>Email</th>
                <th class="w-25"></th>
              </tr>
              </thead>
              <tbody v-if="participants.data.length > 0">
              <tr v-for="(participant, key) in participants.data" :key="`participant-${key}`">
                <td>{{ key + 1 }}</td>
                <td><small class="text-info">{{
                    participant.alt_id ? participant.alt_id + ' - ' : ''
                  }}</small>{{ participant.name }}
                </td>
                <td>{{ participant.username }}</td>
                <td>{{ participant.email }}</td>
                <td>
                  <button v-if="!isSelectedParticipant(participant)" class="btn btn-sm btn-outline-primary"
                          @click="chooseParticipant(participant)">
                    <i class="fas fa-plus"></i>
                  </button>
                  <button v-else class="btn btn-sm btn-outline-danger" @click="retractParticipant(participant)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              </tbody>
              <tbody v-else>
              <tr>
                <td colspan="5" style="text-align: center">No Records</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Datetime} from 'vue-datetime'
import vSelect from 'vue-select'

import 'vue-datetime/dist/vue-datetime.css'
import 'vue-select/dist/vue-select.css';

export default {
  name: "CreateNewExam",
  props: {
    packages: {
      required: true
    },
    exam: {
      type: Object,
      required: false,
    }
  },
  components: {Datetime, vSelect},
  data() {
    return {
      form: {
        name: "",
        package_id: "",
        is_anytime: false,
        scheduled_at: null,
        ended_at: null,
        duration: 0,
        is_multi_attempt: false
      },
      participantQuery: null,
      selectedParticipants: [],
      participants: {
        data: [],
      }
    }
  },
  created() {
    this.getParticipants()

    if (this.exam) {
      this.form.name = this.exam.name
      this.form.package_id = this.exam.package_id
      this.form.is_anytime = this.exam.is_anytime
      this.form.scheduled_at = moment(this.exam.scheduled_at).toISOString()
      this.form.ended_at = this.exam.ended_at
      this.form.duration = this.exam.duration
      this.form.is_multi_attempt = this.exam.is_multi_attempt

      this.selectedParticipants = this.exam.participants
    }
  },
  watch: {
    participantQuery: function (val) {
      if (val) {
        this.getParticipants()
      }
    }
  },
  methods: {
    isSelectedParticipant: function (participant) {
      return this.selectedParticipants.findIndex(selectedParticipant => selectedParticipant.hash === participant.hash) > -1
    },
    getParticipants: _.debounce(function () {
      this.request('api.back-office.user.participant', {
        keyword: this.participantQuery
      }, null, false).then(response => {
        this.participants = response.data
      })
    }, 500),
    chooseParticipant(participant) {
      this.selectedParticipants.push(participant)
    },
    retractParticipant(participant) {
      this.selectedParticipants = this.selectedParticipants.filter(selectedParticipant => selectedParticipant.hash !== participant.hash)
    },
    save() {
      const scheduled_at = moment(this.form.scheduled_at, moment.ISO_8601).format("YYYY-MM-DD HH:mm:ss")
      const participants = this.selectedParticipants.map(participant => participant.hash)

      const callback = res => {
        this.swal(res.data.status, res.data.message, res.data.status).then(() => this.$emit('success'))
      }
      const data = {
        ...this.form,
        scheduled_at,
        participants,
      }

      if (this.exam) {
        this.request('api.back-office.exam.update', { exam: this.exam.id }, {
          data
        }).then(callback)
      } else {
        this.request('api.back-office.exam.store', {}, {
          data
        }).then(callback)
      }
    }
  }
}
</script>

<style scoped>

</style>
