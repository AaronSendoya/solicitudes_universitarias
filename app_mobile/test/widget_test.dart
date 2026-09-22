import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:app_mobile/main.dart';

void main() {
  testWidgets('App arranca y muestra la pantalla inicial', (WidgetTester tester) async {
    await tester.pumpWidget(const CampusConnectApp());
    await tester.pump();

    expect(find.byType(MaterialApp), findsOneWidget);
  });
}
