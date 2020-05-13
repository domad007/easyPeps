import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, AsyncStorage } from 'react-native';

class Cours extends Component {
    constructor(props){
        super(props);
        this.state = {
            cours: []
        }
    }
    
    componentDidMount(){
        AsyncStorage.getItem('idGroup').then(this.cours);
    }
    cours = (value) => {
        fetch('http://192.168.1.3/coursUser/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                alert("Vous n'avez pas de cours veuillez en créer sur notre site web")
            }
            else {
                this.setState({cours: responseJson})
            }
        })
    }
    render(){
        let coursUser = this.state.cours;
        let afficheCours = [];
        for(let i = 0; i < coursUser.length; i++){
            afficheCours.push(
                <View key = { i } style={style.container}>
                    <Text style={{ fontSize: 20 }}>{coursUser[i]['cours']}</Text>
                    <Text>Date de cours: {coursUser[i]['date_cours']}</Text>
                    <Text>Nombre d'heures: {coursUser[i]['heures']}</Text>
                    <Text>Periode: {coursUser[i]['periode']}</Text>
                </View>
            )
        }
        return (
            <ScrollView>
                { afficheCours }
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
export default Cours