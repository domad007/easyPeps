import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, AsyncStorage } from 'react-native';
import { DataTable } from 'react-native-paper';

class Eleves extends Component {
    constructor(props){
        super(props);
        this.state = {
            eleves: []
        }
    }
    
    componentDidMount(){
        AsyncStorage.getItem('idGroup').then(this.eleves);
    }
    eleves = (value) => {
        fetch('http://192.168.1.3/userEleves/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                alert("Vous n'avez pas créé d'élèves, veuillez en créer sur notre site web")
            }
            else {
                this.setState({eleves: responseJson})
            }
        })
    }
    render(){
        let eleves = this.state.eleves
        let afficheEleves = [];
    
        for(let i = 0; i<eleves.length; i++){
            afficheEleves.push(
                <DataTable.Row>
                    <DataTable.Cell>{eleves[i]['nom']}</DataTable.Cell>
                    <DataTable.Cell>{eleves[i]['prenom']}</DataTable.Cell>
                    <DataTable.Cell>{eleves[i]['classe']}</DataTable.Cell>
                    <DataTable.Cell>{eleves[i]['age']}</DataTable.Cell>
                </DataTable.Row>
            )
        }
        return (
            <ScrollView>
                <DataTable>
                    <DataTable.Header>
                        <DataTable.Title>Nom</DataTable.Title>
                        <DataTable.Title>Prénom</DataTable.Title>
                        <DataTable.Title>Classe</DataTable.Title>
                        <DataTable.Title>Age</DataTable.Title>
                    </DataTable.Header>

                    { afficheEleves }
                </DataTable>
            </ScrollView>
        )
    }
}
export default Eleves