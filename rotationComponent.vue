var RotationComponent = {
  name: 'RosterComponent',
  template: `
    <div class='container'>
      <b-row>
        <b-col cols='2' class='text-center'>
          <a role='button' class='btn btn-lg btn-link' href='#' v-on:click='switchToRoster();'>Back</a>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <a role='button' class='btn btn-lg btn-link'href='/jhmlhome.html'>JHML Home</a>
        </b-col>
      </b-row>
      <h1><center>{{ team.team_name }}</center></h1>
      <b-row v-for='rotations in chunkedRotation' v-bind:key='rotations[0].game'>
        <b-col class='fixed mt-2' cols='12' sm='6' lg='3' v-for='rot in rotations' v-bind:key='rot.game'>
          <span v-html='pprintGame(rot.game)' />: <b-form-select style='width:80%' v-model='rot.pitcher' v-bind:options='options' v-on:change='calculateStarts()'/>
        </b-col>
      </b-row>
      <b-row class='float-right'>
        <b-col cols='12'>
          <b-button v-on:click='onSubmit' variant='primary' class='mr-4'>Submit</b-button>
          <b-button v-on:click='onReset' variant='danger' class='mr-4'>Reset</b-button>
        </b-col>
      </b-row>
      <br/>
      <b-row>
        <b-col cols='12' lg='8'>
          <b-table striped hover small outlined bordered v-bind:items="starters" v-bind:fields="fields">
            <template slot="innings" slot-scope="data">
              {{ data.item.innings == undefined ? 0 : data.item.innings }}
            </template>
            <template slot="starts" slot-scope="data" class="float-right">
              {{ data.item.starts == undefined ? 0 : data.item.starts }}
            </template>
            <template v-slot:cell(innings_per_start)="data">
              {{ data.item.starts == 0 ? '-' : (data.item.innings / data.item.starts).toFixed(1) }}
            </template>
          </b-table>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
      team: {"team_name":""},
      teamname: 't',
      starters: [],
      options: [],
      fields: [
        'name',
        {key: 'innings', tdClass: "text-right", thClass: "text-center"},
        {key: 'starts', tdClass: "text-right", thClass: "text-center"},
        {key: 'innings_per_start', tdClass: "text-right", thClass: "text-center"},
      ],
    }
  },
  computed: {
    chunkedRotation() {
       if (vue == undefined) return;
       if (vue.rotation == undefined || vue.rotation.rotation == undefined) return [];
       return _.chunk(vue.rotation.rotation.rotation,4);
    },
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
    eBus.$on('rosterUpdated',(r) => { this.rosterChanged();})
    eBus.$on('rotationUpdated',(r) => { this.calculateStarts(); })
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  methods: {
    currentComponent() { return vue.currentComponent; },
    rosterChanged() {
      if (vue == undefined) return;
      if (vue.roster == undefined || vue.roster == null) return;
      this.options = [{'value': '', text: 'Not Scheduled'}];
      this.starters = [];
      if (vue.roster.roster.pitchers) {
        for (b of vue.roster.roster.pitchers) {
          //console.log(b.rosterItem.player);
          if (b.rosterItem.player.strat.endurance.includes('S(')) {
            this.options.push({'value':b.rosterItem.player.name,'text':b.rosterItem.player.name});
            this.starters.push({'name':b.rosterItem.player.name,'innings':(Math.trunc(b.rosterItem.player.strat.ip*102/162*1.1*3)/3).toFixed(1),'starts':0});
          }
        }
        this.calculateStarts();
      }
    },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
    switchToRoster() {
      vue.currentComponent='rosterComponent';
      vue.emitData();
    },
    pprintGame(g) {
      if (g < 10) return '&nbsp;&nbsp;' + g;
      if (g < 100) return '&nbsp;' + g;
      return g;
    },
    onSubmit(evt) {
      evt.preventDefault();
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/putRotation.php',{data: vue.rotation},headers)
      .then(function (response) {
        //console.log(response);
        self.switchToRoster();
      })
      .catch(function (error) {
        console.error(error);
        self.switchToRoster();
      });
    },
    onReset(evt) {
      evt.preventDefault();
      vue.loadRotation();
    },
    calculateStarts() {
      for (s of this.starters) s.starts = 0;
      for (p of vue.rotation.rotation.rotation) {
        if (p.pitcher == '') continue;
        for (s of this.starters) {
          if (p.pitcher == s.name) {
            s.starts ++;
          }
        }
      }
    }
  },
};

