import 'package:flutter/material.dart';
import '../core/api_service.dart';

class VerificationDialog extends StatefulWidget {
  final int scheduleId;
  final int busStopId;
  final bool isHindi;

  const VerificationDialog({
    super.key,
    required this.scheduleId,
    required this.busStopId,
    required this.isHindi,
  });

  @override
  State<VerificationDialog> createState() => _VerificationDialogState();
}

class _VerificationDialogState extends State<VerificationDialog> {
  String _reportType = 'on_time';
  double _delayMinutes = 0;
  final TextEditingController _notesController = TextEditingController();
  bool _isSubmitting = false;

  Future<void> _submit() async {
    setState(() => _isSubmitting = true);

    final success = await ApiService.submitVerification(
      scheduleId: widget.scheduleId,
      busStopId: widget.busStopId,
      reportType: _reportType,
      delayMinutes: _delayMinutes.toInt(),
      notes: _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
    );

    if (!mounted) return;
    setState(() => _isSubmitting = false);

    Navigator.pop(context);

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          success
              ? (widget.isHindi ? 'सत्यापन दर्ज किया गया। धन्यवाद!' : 'Verification submitted. Thank you!')
              : (widget.isHindi ? 'सत्यापन सबमिट नहीं हो सका।' : 'Submission failed.'),
        ),
        backgroundColor: success ? const Color(0xFF059669) : Colors.red,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(
        left: 20,
        right: 20,
        top: 20,
        bottom: MediaQuery.of(context).viewInsets.bottom + 24,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                widget.isHindi ? 'कम्युनिटी बस सत्यापन' : 'Community Bus Verification',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
              ),
              IconButton(
                icon: const Icon(Icons.close),
                onPressed: () => Navigator.pop(context),
              ),
            ],
          ),
          Text(
            widget.isHindi
                ? 'आपके फीडबैक से अन्य यात्रियों को सही समय पता चलता है।'
                : 'Your feedback helps fellow commuters get accurate real-time data.',
            style: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 18),

          // Report Type Selector
          SegmentedButton<String>(
            segments: [
              ButtonSegment(
                value: 'on_time',
                label: Text(widget.isHindi ? 'समय पर' : 'On Time', style: const TextStyle(fontSize: 11)),
                icon: const Icon(Icons.check_circle_outline, size: 16),
              ),
              ButtonSegment(
                value: 'delayed',
                label: Text(widget.isHindi ? 'देरी' : 'Delayed', style: const TextStyle(fontSize: 11)),
                icon: const Icon(Icons.access_time, size: 16),
              ),
              ButtonSegment(
                value: 'cancelled',
                label: Text(widget.isHindi ? 'रद्द' : 'Cancelled', style: const TextStyle(fontSize: 11)),
                icon: const Icon(Icons.cancel_outlined, size: 16),
              ),
            ],
            selected: {_reportType},
            onSelectionChanged: (val) {
              setState(() => _reportType = val.first);
            },
          ),

          if (_reportType == 'delayed') ...[
            const SizedBox(height: 16),
            Text(
              '${widget.isHindi ? 'कितनी देरी?' : 'How much delay?'}: ${_delayMinutes.toInt()} ${widget.isHindi ? 'मिनट' : 'mins'}',
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
            ),
            Slider(
              value: _delayMinutes,
              min: 0,
              max: 90,
              divisions: 18,
              label: '${_delayMinutes.toInt()} mins',
              activeColor: const Color(0xFFD97706),
              onChanged: (val) => setState(() => _delayMinutes = val),
            ),
          ],

          const SizedBox(height: 14),
          TextField(
            controller: _notesController,
            decoration: InputDecoration(
              labelText: widget.isHindi ? 'टिप्पणी (वैकल्पिक)' : 'Notes (Optional)',
              hintText: widget.isHindi ? 'उदा. ट्रैफिक जाम, बारिश, बस स्टॉप पर भीड़' : 'e.g. traffic jam, heavy rain',
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
              isDense: true,
            ),
            maxLines: 2,
          ),

          const SizedBox(height: 18),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _isSubmitting ? null : _submit,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF4338CA),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: _isSubmitting
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : Text(widget.isHindi ? 'सबमिट करें (Submit)' : 'Submit Verification', style: const TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }
}

