import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, flexDirection, AsyncStorage } from 'react-native';
import { DataTable } from 'react-native-paper';
class CahierCotes extends Component {
    constructor(props){
        super(props);
        this.state = {
            idGroup: ""
        }
    }
    loadGroup = async () => {
        let groupId = await AsyncStorage.getItem('idGroup');
        this.setState({idGroup: groupId})
    }

    componentDidMount(){
        this.loadGroup().done()
    }
    render(){
        
        let ecoles = [];
        for(let i = 0; i<15; i++){
            ecoles.push(
                <DataTable.Row>
                    <DataTable.Cell>Jean</DataTable.Cell>
                    <DataTable.Cell>Michel</DataTable.Cell>
                    <DataTable.Cell>6G1</DataTable.Cell>
                    <DataTable.Cell>18</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
                    <DataTable.Cell>5</DataTable.Cell>
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
                        <DataTable.Title>P1</DataTable.Title>
                        <DataTable.Title>P2</DataTable.Title>
                        <DataTable.Title>P3</DataTable.Title>
                        <DataTable.Title>P4</DataTable.Title>
                        <DataTable.Title>Semestre 1</DataTable.Title>
                        <DataTable.Title>Semestre 2</DataTable.Title>
                        <DataTable.Title>Année</DataTable.Title>
                        
                    </DataTable.Header>

                    { ecoles }
                </DataTable>
            </ScrollView>
        )
    }
}

export default CahierCotes