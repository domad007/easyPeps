import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, AsyncStorage } from 'react-native';

class Evaluations extends Component {
    constructor(props){
        super(props);
        this.state = {
            evaluations: []
        }
    }
    
    componentDidMount(){
        AsyncStorage.getItem('idGroup').then(this.evaluations);
    }
    evaluations = (value) => {
        fetch('http://192.168.1.3/evaluationUser/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                alert("Vous n'avez pas d'évaluation' veuillez en créer sur notre site web")
            }
            else {
                this.setState({evaluations: responseJson})
            }
        })
    }
    render(){
        let evaluations = this.state.evaluations
        let afficheEval = [];
        for(let i = 0; i<evaluations.length; i++){
            afficheEval.push(
                <View key = { i } style={style.container}>
                    <Text style={{ fontSize: 20 }}>{ evaluations[i]['evaluation'] }</Text>
                    <Text>Date de l'évaluation: { evaluations[i]['date_evaluation'] }</Text>
                    <Text>Herues d'évaluation: { evaluations[i]['heures'] }</Text>
                    <Text>Compétence: { evaluations[i]['competence'] }</Text>
                    <Text>Cotation: { evaluations[i]['sur_combien'] }</Text>
                    <Text>Periode: { evaluations[i]['periode'] }</Text>
                </View>
            )
        }
        return (
            <ScrollView>
                { afficheEval }
            </ScrollView>
        )
    }
}

const style= StyleSheet.create({
    container: {
        flex : 1,
        backgroundColor: '#fff',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 10,
        marginLeft: 10,
        width: '95%',
        backgroundColor:'lightgrey',
        borderRadius: 25,
        marginVertical: 10,
        paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    }
})
export default Evaluations