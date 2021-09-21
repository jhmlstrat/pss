var SituationComponent = {
  name: 'SituationComponent',
  template: `
    <div class='container'>
      <b-row no-gutters style="background-color: white;">
        <b-col cols="3" class="text-center">
          <b-row no-gutters fill-height class='h-100'>
            <b-col cols="6" v-bind:style="getStyle('visitor')" class="pt-2">
              <span class="h5 text-uppercase text-monospace text-white">{{gameInfo.visitor.name}}<br>{{ gameInfo.situation.situation.runs[0] }}</span>
            </b-col>
            <b-col cols="6" v-bind:style="getStyle('home')" class="pt-2">
              <span class="h5 text-uppercase text-monospace text-white">{{gameInfo.home.name}}<br>{{ gameInfo.situation.situation.runs[1] }}</span>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="2" class="text-center border border-dark border-top-0 border-bottom-0">
          <b-row no-gutters>
            <b-col cols="12" class="border-bottom border-dark mt-2 pb-1">
            {{ gameInfo.situation.situation.side == 0 ? 'Top' : 'Bot' }} {{ gameInfo.situation.situation.inning }}
            </b-col>
          </b-row>
          <b-row no-gutters>
            <b-col cols="12" class="border-top border-dark pt-2">
            Outs: {{ gameInfo.situation.situation.outs }}
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="2" class="pt-1 pb-1 border border-dark border-top-0 border-bottom-0" >
          <b-row no-gutters class="mt-1">
            <b-col cols="3">
            </b-col>
            <b-col cols="4" class="text-center">
              <b-icon icon="square" rotate="45" font-scale="1.0" v-if="gameInfo.situation.situation.second == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="1.0" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.second" v-on:click="doPinchRunner(2)" v-else></b-icon>
            </b-col>
            <b-col cols="3">
            </b-col>
          </b-row>
          <b-row no-gutters class="mt-2">
            <b-col cols="3" class="text-left">
              <b-icon icon="square" rotate="45" font-scale="1.0" v-if="gameInfo.situation.situation.third == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="1.0" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.third" v-on:click="doPinchRunner(3)" v-else></b-icon>
            </b-col>
            <b-col cols="2">
            </b-col>
            <b-col cols="3" class="text-right">
              <b-icon icon="square" rotate="45" font-scale="1.0" v-if="gameInfo.situation.situation.first == ''"></b-icon>
              <b-icon icon="square-fill" rotate="45" font-scale="1.0" v-b-tooltip.hover v-bind:title="gameInfo.situation.situation.first" v-on:click="doPinchRunner(1)" v-else></b-icon>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="5" class="border border-dark border-top-0 border-bottom-0">
              <b-button class="pr-0 pl-0 mr-0 ml-0 text-left" variant="link" href="#" v-on:click="doReliever()" style="padding: .375rem 0rem">
Pitch: {{ gameInfo.situation.situation.pitcher.substring(0,15) }}
              </b-button>
          <b-row no-gutters style="background-color: white;">
            <b-col cols="12" class="border-top border-dark text-left">
              <b-button class="pr-0 pl-0 pt-0 pb-0" variant="link" href="#" v-on:click="doPinchHitter()" style="padding: .375rem 0rem">
Bat: {{ gameInfo.situation.situation.batter.substring(0,15) }}
              </b-button>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
      <b-modal id="relief-modal" ref="modal" size="md" title="Change Pitchers" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="12">
              <b-form-group label="Select Reliever">
                <b-form-radio v-for="(p, index) in available_pitchers" v-model="selected_reliever" name="reliever-radios" v-bind:value="p" v-bind:key="index">
                  {{ p }}
                </b-form-radio>
              </b-form-group>
            </b-col>
          </b-row>
          <hr class="pl-0 pr-0 w-100">
          <b-button size="sm" class="float-right" variant="secondary" @click="cancelReliever()">
            Cancel
          </b-button>
          <b-button size="sm" class="float-right" variant="success" @click="handleReliever()">
            OK
          </b-button>
          </span>
        </template>
      </b-modal>
      <b-modal id="pinch-hitter-modal" ref="modal" size="md" title="Change Hitters" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="12">
              <b-form-group label="Select Pinch Hitter">
                <b-form-radio v-for="(p, index) in available_hitters" v-model="selected_hitter" name="hitter-radios" v-bind:value="p" v-bind:key="index">
                  {{ p }}
                </b-form-radio>
              </b-form-group>
            </b-col>
          </b-row>
          <hr class="pl-0 pr-0 w-100">
          <b-button size="sm" class="float-right" variant="secondary" @click="cancelPinchHitter()">
            Cancel
          </b-button>
          <b-button size="sm" class="float-right" variant="success" @click="handlePinchHitter()">
            OK
          </b-button>
          </span>
        </template>
      </b-modal>
      <b-modal id="pinch-runner-modal" ref="modal" size="md" title="Change Runners" hide-header-close hide-footer>
        <template #default="{ ok }">
          <b-row>
            <b-col cols="12">
              <b-form-group label="Select pinch Runner">
                <b-form-radio v-for="(p, index) in available_hitters" v-model="selected_runner" name="runner-radios" v-bind:value="p" v-bind:key="index">
                  {{ p }}
                </b-form-radio>
              </b-form-group>
            </b-col>
          </b-row>
          <hr class="pl-0 pr-0 w-100">
          <b-button size="sm" class="float-right" variant="secondary" @click="cancelPinchRunner()">
            Cancel
          </b-button>
          <b-button size="sm" class="float-right" variant="success" @click="handlePinchRunner()">
            OK
          </b-button>
          </span>
        </template>
      </b-modal>
    </div>
  `,
  props: ['gameInfo','vRoster','hRoster'],
  data: function() {
    return {
        available_hitters: [],
        available_pitchers: [],
        selected_hitter: '',
        selected_reliever: '',
        selected_runner: '',
    }
  },
  watch: {
  },
  mounted() {
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    getStyle(s) {
      if (s=='visitor') t=this.gameInfo.visitor.name;
      else t=this.gameInfo.home.name;
      style = 'background-color: white;';
      for (p of vue.config.colors) {
        if (Object.keys(p)[0] == t) style = 'background-color: ' + Object.values(p)[0] + '; padding-top: 0.8rem !important;';
      }
      return style;
    },
    cancelPinchHitter() {
      this.$bvModal.hide('pinch-hitter-modal');
    },
    cancelPinchRunner() {
      this.$bvModal.hide('pinch-runner-modal');
    },
    cancelReliever() {
      this.$bvModal.hide('relief-modal');
    },
    doPinchHitter() {
      // TBD: Pinch hitting pitcher
      if (this.gameInfo.situation.situation.side == 0) {
        available = vue.getAvailable(this.gameInfo.visitor.lineup, this.gameInfo.visitor.rotation, this.vRoster)
      } else {
        available = vue.getAvailable(this.gameInfo.home.lineup, this.gameInfo.home.rotation, this.hRoster)
      }
      //console.log(available);
      this.available_hitters = [];
      this.selected_hitter = '';
      for (let i=0; i < available.availableBatters.length; i++) {
        this.available_hitters.push(available.availableBatters[i].name);
      }
      this.$bvModal.show('pinch-hitter-modal');
    },
    doPinchRunner(base) {
      // TBD: Pinch running pitcher
      //console.log('doPinchRunner - ' + base);
      if (this.gameInfo.situation.situation.side == 0) {
        available = vue.getAvailable(this.gameInfo.visitor.lineup, this.gameInfo.visitor.rotation, this.vRoster)
      } else {
        available = vue.getAvailable(this.gameInfo.home.lineup, this.gameInfo.home.rotation, this.hRoster)
      }
      //console.log(available);
      this.available_hitters = [];
      this.selected_runner = '';
      this.base = base
      for (let i=0; i < available.availableBatters.length; i++) {
        this.available_hitters.push(available.availableBatters[i].name);
      }
      this.$bvModal.show('pinch-runner-modal');
    },
    doReliever() {
      // TBD: Batting pitching
      if (this.gameInfo.situation.situation.side == 0) {
        available = vue.getAvailable(this.gameInfo.home.lineup, this.gameInfo.home.rotation, this.hRoster)
      } else {
        available = vue.getAvailable(this.gameInfo.visitor.lineup, this.gameInfo.visitor.rotation, this.vRoster)
      }
      //console.log(available);
      this.available_pitchers = [];
      this.selected_reliever = '';
      for (let i=0; i < available.availablePitchers.length; i++) {
        this.available_pitchers.push(available.availablePitchers[i].name);
      }
      this.$bvModal.show('relief-modal');
    },
    handlePinchHitter() {
      this.$bvModal.hide('pinch-hitter-modal');
      if (this.selected_hitter == '') return;
      //console.log(this.gameInfo);
      //console.log(this.selected_hitter);
//TBD
    },
    handlePinchRunner() {
      this.$bvModal.hide('pinch-runner-modal');
      if (this.selected_runner == '') return;
      //console.log(this.selected_base);
      //console.log(this.selected_runner);
//TBD
    },
    handleReliever() {
      this.$bvModal.hide('relief-modal');
      if (this.selected_reliever == '') return;
      //console.log(this.selected_reliever);
      if (this.gameInfo.situation.situation.side == 0) {
        for (let i=0; i < this.hRoster.roster.pitchers.length; i++) {
          if (this.hRoster.roster.pitchers[i].rosterItem.player.name == this.selected_reliever) {
            this.gameInfo.home.rotation.push({'player':{'name':this.selected_reliever}});
            i = this.hRoster.roster.pitchers.length;
          }
        }
      } else {
        for (let i=0; i < this.vRoster.roster.pitchers.length; i++) {
          if (this.vRoster.roster.pitchers[i].rosterItem.player.name == this.selected_reliever) {
            this.gameInfo.visitor.rotation.push({'player':{'name':this.selected_reliever}});
            i = this.vRoster.roster.pitchers.length;
          }
        }
      }
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/updateLineup.php',{data: { 'year':vue.year,
                                                     'game':this.gameInfo}}
                ,headers)
        .then(function (response) {
          vue.loadGameInfo();
        })
        .catch(function (error) {
          console.error(error);
      });
    },
  },
};

