var LineupComponent = {
  name: 'LineupComponent',
  template: `
    <div class='container'>
<!--
{{ side }}
<hr>
{{roster}}
<hr>
{{rotation}}
<hr>
-->
      <b-tabs>
        <b-tab title="Lineup">
          <b-row class='mt-3'>
            <b-col cols='6'>
              <b-row>
                <b-col cols="1">
                </b-col>
                <b-col>
                  Opposing Pitcher: {{ ((!oside.rotation) || oside.rotation.length == 0) ? "Unassigned" : oside.rotation[oside.rotation.length - 1].player.name + '(' +  oPitchHand(oside.rotation[oside.rotation.length - 1].player.name) + ')'}}
                </b-col>
              </b-row>
              <b-row  v-for="(sB,index) in selectedB" v-bind:key="index" class="mt-1">
                <b-col cols="1">
                  {{ index + 1 }}
                </b-col>
                <b-col cols="11">
<!--
                  <b-table hover small v-bind:items="l_lineup[index]" v-bind:fields="lfields" thead-class="d-none">
                  </b-table>
-->
                  <b-row v-for="(bat, iindex) in l_lineup[index]" v-bind:key="iindex">
                    <b-col cols="6">
                      <span class="text-danger" v-if="bat.injured">
                        {{bat.name}}
                      </span>
                      <span v-else>
                        {{bat.name}}
                      </span>
                    </b-col>
                    <b-col cols="2">
                      {{bat.positions}}
                    </b-col>
                    <b-col cols="4">
                      <b-button v-on:click='onMoveB(index)' size='sm' variant='primary' class='float-right ml-1 mb-1' v-show="iindex == l_lineup[index].length - 1">Move</b-button>
                      <b-button v-on:click='onChangeB(index)' size='sm' variant='primary' class='float-right mr-1 mb-1' v-show="iindex == l_lineup[index].length - 1">Change</b-button>
                    </b-col>
                  </b-row>
                  <b-row v-show="l_lineup[index].length == 0 || l_change[index]" no-gutters>
                    <b-col cols="10" class="pr-2">
                      <b-form-select v-model="selectedB[index]" v-bind:options="batterSelections" v-show="! l_moveOnly[index]"></b-form-select>
                    </b-col>
                    <b-col cols="2" class="pr-2">
                      <b-form-select v-model="selectedPos[index]" v-bind:options="posSelections"></b-form-select>
                    </b-col>
                  </b-row>
                </b-col>
              </b-row>
              <b-row v-show="errorString != ''" class="mt-3">
                <b-col cols="1">
                </b-col>
                <b-col>
                  <b-alert show variant="danger" v-html="errorString"></b-alert>
                </b-col>
              </b-row>
              <b-row v-show="warnString != ''" class="mt-3">
                <b-col cols="1">
                </b-col>
                <b-col>
                  <b-alert show variant="warning" v-html="warnString"></b-alert>
                </b-col>
              </b-row>
              <b-button v-on:click='onSubmitB' variant='primary' class='mt-3 mr-3 float-right' v-show="lineupValid() && ! submittedB">Submit</b-button>
            </b-col>
            <b-col cols='6'>
              <b-row class='mb-2'>
                <b-col cols='12'>
                  <span class="h5">Available:</span>
                </b-col>
              </b-row>
              <b-table striped hover small outlined bordered v-bind:items="availableBatters" v-bind:fields="bfields">
                <template v-slot:cell(hand)="data">
                  {{ data.item.strat.hand }}
                </template>
                <template v-slot:cell(fielding)="data">
                  {{ formatPositions(data.item.strat.positionsPlayed) }}
                </template>
              </b-table>
            </b-col>
          </b-row>
        </b-tab>
        <b-tab title="Rotation" v-bind:active="side.rotation && side.rotation.length == 0">
          <b-row class='mt-3'>
            <b-col cols='6'>
              <!-- Insert previous pitchers -->
              <b-table hover small v-bind:items="l_rotation" v-bind:fields="rfields" thead-class="d-none">
              </b-table>
              <b-form-select v-model='selectedP' v-bind:options='pitcherSelections' v-show="! submittedP"></b-form-select>
              <b-button v-on:click='onSubmitP' variant='primary' class='mt-3 mr-3 float-right' v-show="! submittedP && selectedP!= ''">Submit</b-button>
            </b-col>
            <b-col cols='6'>
              <b-row class='mb-2'>
                <b-col cols='12'>
                  <span class="h5">Available:</span>
                </b-col>
              </b-row>
              <b-table striped hover small outlined bordered v-bind:items="availablePitchers" v-bind:fields="pfields">
                <template v-slot:cell(hand)="data">
                  {{ data.item.strat.hand }}
                </template>
                <template v-slot:cell(endurance)="data">
                  {{ data.item.strat.endurance }}
                </template>
              </b-table>
            </b-col>
          </b-row>
        </b-tab>
<!--
<hr>
{{oside}}
<hr>
{{oroster}}
-->
      </b-tabs>
    </div>
  `,
  props: ['side','oside','roster','oroster','rotation'],
  data: function() {
    return {
      availableBatters: [],
      batterSelections: [],
      selectedB: [],
      selectedPos: [],
      availablePitchers: [],
      pitcherSelections: [],
      posSelections: [{'value':'P','text':'P'},
                      {'value':'C','text':'C'},
                      {'value':'1B','text':'1B'},
                      {'value':'2B','text':'2B'},
                      {'value':'3B','text':'3B'},
                      {'value':'SS','text':'SS'},
                      {'value':'LF','text':'LF'},
                      {'value':'CF','text':'CF'},
                      {'value':'RF','text':'RF'},
                      {'value':'DH','text':'DH'}],
      selectedB: ['','','','','','','','',''],
      selectedPos: ['','','','','','','','',''],
      selectedP: '',
      submittedB: false,
      submittedP: false,
      bfields: [
        'name',
        'hand',
        'fielding',
      ],
      pfields: [
        'name',
        'hand',
        'endurance',
      ],
      lfields: [
        'name',
        'positions',
        'actions',
      ],
      rfields: [
        'name',
      ],
      l_lineup: [[],[],[],[],[],[],[],[],[]],
      l_change: [false,false,false,false,false,false,false,false,false],
      l_moveOnly: [false,false,false,false,false,false,false,false,false],
      l_rotation: [],
      errorString: '',
      warnString: '',
    }
  },
  watch: {
    side() {
      this.setAvailableBatters();
      this.setAvailablePitchers();
    },
    roster() {
      this.setAvailableBatters();
      this.setAvailablePitchers();
    },
  },
  mounted() {
    this.setAvailableBatters();
    this.setAvailablePitchers();
  },
  methods: {
    lineupValid() {
      let l = JSON.parse(JSON.stringify(this.side.lineup));
      change = false;
      for (let i = 0; i < this.selectedB.length; i++) {
        b = this.selectedB[i];
        if (b != '') {
          change = true;
          a = {'player':{'name':b,'positions':[],'injured':false}};
          if (this.selectedPos[i] != '') a.player.positions.push({'position':{'pos':this.selectedPos[i]}});
          l[i].push(a);
        }
      }
      lv = vue.lineupValid(l);
      if (lv.playerOutOfPosition) this.warnString = 'Player out of position';
      else this.warnString = '';
      errorString = '';
      if (lv.injuredPlayer) {
        errorString += 'Injured players assigned';
      }
      if (lv.duplicatePlayer) {
        errorString += 'Duplicate players assigned';
      }
      if (lv.duplicatePosition) {
        if (errorString != '') errorString += '<br/>';
        errorString += 'Duplicate positions assigned';
      }
      if (lv.missingPlayer) {
        if (errorString != '') errorString += '<br/>';
        errorString += 'Not all players assigned';
      }
      if (lv.missingPosition) {
        if (errorString != '') errorString += '<br/>';
        errorString += 'Not all positions assigned';
      }
      this.errorString = errorString;
      return change && lv.valid;
    },
    setAvailableBatters() {
      for (i=0; i<this.side.lineup.length;i++) {
        this.l_lineup[i] = [];
        for (lu of this.side.lineup[i]) {
          injured = false;
          if ('injured' in lu.player) injured = lu.player.injured;  
          this.l_lineup[i].push({'name':lu.player.name,'positions':this.formatInGamePositions(lu.player.positions),'injured':injured});
        }
      }
      available = vue.getAvailable(this.side.lineup, this.side.rotation, this.side.roster, this.roster)
      this.availableBatters = available.availableBatters
      this.batterSelections=[{'value':'','text':''}];
      for (b of this.availableBatters) { this.batterSelections.push({'value':b.name,'text':b.name}); }
    },
    setAvailablePitchers() {
      this.l_rotation = [];
      if (! this.side.rotation) return [];
      for (p of this.side.rotation) {
        this.l_rotation.push({'name':p.player.name});
      }
      available = vue.getAvailable(this.side.lineup, this.side.rotation, this.side.roster, this.roster)
      this.availablePitchers = [];
      starters = [];
      sr = [];
      relievers = [];
      for (p of available.availablePitchers) {
        s = p.strat.endurance.includes('S');
        r = p.strat.endurance.includes('R');
        if (s && r) sr.push(p);
        else if (s) starters.push(p);
        else relievers.push(p);
      }
      if (this.side.rotation.length == 0) this.availablePitchers = starters.concat(sr.concat(relievers));
      else this.availablePitchers = relievers.concat(sr.concat(starters));
      this.pitcherSelections = [{'value':'','text':''}];
      for (p of this.availablePitchers) {
        if (this.side.rotation.length == 0 && this.rotation.rotation.rotation[parseInt(this.side.gameNumber) - 1].pitcher == p.name) this.selectedP = p.name;
        this.pitcherSelections.push({'value':p.name,'text':p.name});
      }
    },
    formatPositions(pos) {
      rtn = '';
      arm = 0;
      for (p of pos) {
        if (rtn != '') rtn += ', ';
        ppos = p.position;
        if (ppos.pos == 'C') {
          rtn += ppos.pos + '-' + ppos.rating + '(' + ppos.arm + ')e' + ppos.e;
        } else if (ppos.pos == 'B1' || ppos.pos == 'B2' || ppos.pos == 'B3') {
          rtn += ppos.pos.slice(1) + 'B-' + ppos.rating + 'e' + ppos.e;
        } else if (ppos.pos == 'LF' || ppos.pos == 'CF' || ppos.pos == 'RF') {
          rtn += ppos.pos + '-' + ppos.rating + '(' + ppos.arm + ')e' + ppos.e;
        } else {
          rtn += ppos.pos + '-' + ppos.rating + 'e' + ppos.e;
        }
      }
      return rtn;
    },
    formatInGamePositions(positions) {
      rtn = '';
      for (p of positions) {
        if (rtn != '') rtn += ', ';
        pm = p.position.pos;
        if (pm == 'B1') pm = '1B';
        if (pm == 'B2') pm = '2B';
        if (pm == 'B3') pm = '3B';
        rtn += pm;
      }
      return rtn;
    },
    onSubmitB() {
      for (let i=0; i<9; i++) {
        if (this.selectedB[i] == '' && this.selectedPos[i] == '') continue;
        pm = this.selectedPos[i];
        if (pm == '1B') pm = 'B1';
        if (pm == '2B') pm = 'B2';
        if (pm == '3B') pm = 'B3';
        l = this.side.lineup[i];
        //console.log(this.side.lineup[i]);
        //if (l.length > 0) console.log(l[l.length - 1].player.name);
        //console.log(this.selectedB[i]);
        if (l.length == 0 || l[l.length - 1].player.name != this.selectedB[i]) {
          l.push({'player':{'name':this.selectedB[i],'positions':[{'position':{'pos':pm}}]}});
        } else {
          if (l[l.length - 1].player.positions[l[l.length - 1].player.positions.length -1].position.pos != this.selectedPos[i]) {
            l[l.length - 1].player.positions.push({'position':{'pos':pm}});
          }
        }
      }
      this.setAvailableBatters();
      this.$emit('lineupUpdate',this.side.lineup);
      this.submittedB = true;
    },
    onSubmitP() {
      this.side.rotation.push({'player':{'name':this.selectedP}});
      this.setAvailablePitchers();
      this.$emit('rotationUpdate',this.side.rotation[this.side.rotation.length -1]);
      this.submittedP = true;
    },
    oPitchHand(pitch) {
      for (p of this.oroster.roster.pitchers) {
        if (p.rosterItem.player.name == pitch) {
          return p.rosterItem.player.strat.hand;
        }
      }
    },
    onMoveB(i) {
      this.selectedB[i] = this.side.lineup[i][this.side.lineup[i].length-1].player.name;
      this.l_change.splice(i, 1, true);
      this.l_moveOnly.splice(i, 1, true);
    },
    onChangeB(i) {
      this.l_change[i] = true;
      this.l_change.splice(i, 1, true);
    },
  },
};

